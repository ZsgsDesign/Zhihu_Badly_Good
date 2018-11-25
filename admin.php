<?php

    require_once("core/conn.php");
    require_once("core/config.php");

    if($strict_mode) exit("Forbidden");

    $process=[];
    foreach($topic_ids as $t){
        $process[$t]=-1;
    }
    $rs=$db->query("select topic_id,LEAST(999,MAX(page_no)) page_max_no from saved_topics group by topic_id;");
    while($row=$rs->fetch()){
        $process[$row["topic_id"]]=intval($row["page_max_no"]);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>管理 - 知乎神回复</title>
        <!-- Necessarily Declarations -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <meta name="renderer" content="webkit">
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="alternate icon" type="image/png" href="https://static.zhihu.com/static/favicon.ico">
        <!-- Loading Style -->
        <link rel="stylesheet" href="https://fonts.geekzu.org/css?family=Roboto:300,300i,400,400i,500,500i,700,700i">
        <link rel="stylesheet" href="https://static.1cf.co/css/bootstrap-material-design.min.css">
        <link rel="stylesheet" href="https://static.1cf.co/fonts/MDI-WXSS/MDI.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <style>
            ::-moz-selection {
                background: #b3d4fc;
                text-shadow: none
            }
            
            ::selection {
                background: #b3d4fc;
                text-shadow: none
            }
            body{
                background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
            }

            card {
                display: block;
                box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
                border-radius: 4px;
                transition: .2s ease-out .0s;
                color: #7a8e97;
                background: #fff;
                padding: 1rem;
                position: relative;
                border: 1px solid rgba(0, 0, 0, 0.15);
                margin-bottom: 2rem;
            }

            card:hover {
                box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 40px;
            }

            h5{
                margin-bottom: 1rem;
            }
            .container{
                padding-top: 5rem;
                padding-bottom: 5rem;
            }
            p{
                margin: 0;
            }
            .MDI{
                cursor:pointer;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php foreach($topic_ids as $t){ ?>
            <card>
                <h5><i class="MDI brightness- text-<?php if($process[$t]<0) echo "warning"; elseif($process[$t]==999) echo "success"; else echo "info"; ?>" id="icon_<?php echo $t; ?>" onclick="get_<?php echo $t; ?>()"></i> 分类 <?php echo $t; ?></h5>
                <div>
                    <p><span id="tot_<?php echo $t; ?>"><?php echo $process[$t]+1; ?></span>页已完成</p>
                </div>
            </card>
            <?php } ?>
        </div>
        <script src="https://static.1cf.co/js/jquery-3.2.1.min.js"></script>
        <script>
            <?php foreach($topic_ids as $t){ ?>
            var use_get_<?php echo $t; ?>=false;
            function get_<?php echo $t; ?>(){
                if(use_get_<?php echo $t; ?>==true){
                    return;
                }
                console.log("开始处理 : <?php echo $t; ?>");
                use_get_<?php echo $t; ?>=true;
                if($("#icon_<?php echo $t; ?>").hasClass("text-success"))return ;
                var page_no_<?php echo $t; ?>=<?php echo $process[$t]+1; ?>;
                var timer_<?php echo $t; ?> = setInterval(function(){
                    if($("#icon_<?php echo $t; ?>").hasClass("text-success"))return ;
                    $.get("ans.php",{
                        "topic_id":<?php echo $t; ?>,
                        "page_no":page_no_<?php echo $t; ?>
                    },function(result){
                        if($("#icon_<?php echo $t; ?>").hasClass("text-success"))return ;
                        result=JSON.parse(result);
                        console.log(result);
                        if(result.ret==200){
                            if($("#tot_<?php echo $t; ?>").html()=="0"){
                                $("#icon_<?php echo $t; ?>").removeClass("text-warning");
                                $("#icon_<?php echo $t; ?>").addClass("text-info");
                            }
                            $("#tot_<?php echo $t; ?>").html(parseInt($("#tot_<?php echo $t; ?>").html())+1);
                            if(result.data.is_end){
                                $("#icon_<?php echo $t; ?>").removeClass("text-info");
                                $("#icon_<?php echo $t; ?>").addClass("text-success");
                                clearInterval(timer_<?php echo $t; ?>);
                            }
                        }
                    });
                    page_no_<?php echo $t; ?>++;
                },1000);
            }
            <?php } ?>
        </script>
    </body>
</html>