<?php

require_once("core/conn.php");

function get_answers_by_page($topic_id, $page_no){
    global $db;
    $offset = $page_no * 10;

    $url = "https://www.zhihu.com/api/v4/topics/$topic_id/feeds/essence?include=data%5B%3F(target.type%3Dtopic_sticky_module)%5D.target.data%5B%3F(target.type%3Danswer)%5D.target.content%2Crelationship.is_authorized%2Cis_author%2Cvoting%2Cis_thanked%2Cis_nothelp%3Bdata%5B%3F(target.type%3Dtopic_sticky_module)%5D.target.data%5B%3F(target.type%3Danswer)%5D.target.is_normal%2Ccomment_count%2Cvoteup_count%2Ccontent%2Crelevant_info%2Cexcerpt.author.badge%5B%3F(type%3Dbest_answerer)%5D.topics%3Bdata%5B%3F(target.type%3Dtopic_sticky_module)%5D.target.data%5B%3F(target.type%3Darticle)%5D.target.content%2Cvoteup_count%2Ccomment_count%2Cvoting%2Cauthor.badge%5B%3F(type%3Dbest_answerer)%5D.topics%3Bdata%5B%3F(target.type%3Dtopic_sticky_module)%5D.target.data%5B%3F(target.type%3Dpeople)%5D.target.answer_count%2Carticles_count%2Cgender%2Cfollower_count%2Cis_followed%2Cis_following%2Cbadge%5B%3F(type%3Dbest_answerer)%5D.topics%3Bdata%5B%3F(target.type%3Danswer)%5D.target.annotation_detail%2Ccontent%2Crelationship.is_authorized%2Cis_author%2Cvoting%2Cis_thanked%2Cis_nothelp%3Bdata%5B%3F(target.type%3Danswer)%5D.target.author.badge%5B%3F(type%3Dbest_answerer)%5D.topics%3Bdata%5B%3F(target.type%3Darticle)%5D.target.annotation_detail%2Ccontent%2Cauthor.badge%5B%3F(type%3Dbest_answerer)%5D.topics%3Bdata%5B%3F(target.type%3Dquestion)%5D.target.annotation_detail%2Ccomment_count&limit=10&offset=$page_no";    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $headers = array();
    $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);

    $data=json_decode($data,true);
    $is_end = $data["paging"]["is_end"];
    $items = $data["data"];

    foreach($items as $i){
        $rs=$db->prepare("insert into answer set id=?,type=?,url=?,question=?,content=?,voteup_count=?");
        if(isset($i["target"]["question"]["title"])) $rs->execute([$i["target"]["id"],$i["target"]["type"],$i["target"]["url"],$i["target"]["question"]["title"],$i["target"]["content"],$i["target"]["voteup_count"]]);
    }
    $rs=$db->prepare("insert into saved_topics set topic_id=?,page_no=?");
    $rs->execute([$topic_id,$page_no]);
    return $is_end;
}

function get_answers($topic_id){
    global $db;
    $page_no = 0;
    while(true){
        $rs=$db->query("select count(*) done from saved_topics where topic_id=$topic_id and page_no=$page_no;");
        $row=$rs->fetch();
        $is_saved=$row["done"];
        if($is_saved){
            echo "$topic_id $page_no already saved";
            $page_no += 1;
            continue;
        }
        echo $topic_id." ".$page_no;
        $is_end = get_answers_by_page($topic_id, $page_no);
        $page_no += 1;
        if($is_end){
            break;
        }
    }
}

function query(){
    global $db;
    $rs=$db->query("select * from answers where voteup_count>=1000 and length(content)<=50;");
    $answer_ids = [];
    while($item=$rs->fetch()) {
        $item_type = $item["type"];
        if($item_type != "answer"){
            continue;
        }
        $question = $item["title"];
        $answer = $item["content"];
        $vote_num = $item["voteup_count"];
        if($vote_num < 1000){
            continue;
        }
        $answer_id = $item["id"];
        if (in_array($answer_id, $answer_ids)) {
            continue;
        }
        $url = $item["url"];
        for($i=1;$i<=50;$i++) echo "-";
        echo "Q: $question\nA: $answer\nvote: $vote_num";
        array_push($answer_ids,$answer_id);
    }
}

$action="save";

if($action=="save"){
    $topic_ids = [19554298, 19552330, 19565652, 19580349, 19939299, 19555547, 19594551, 19552832, 19577377, 19552826, 19615452];
    foreach($topic_ids as $topic_id){
        get_answers($topic_id);
    }
}elseif($action=="query"){
    query();
}