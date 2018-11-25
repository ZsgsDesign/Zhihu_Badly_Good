<?php

require_once("core/conn.php");
$rs=$db->query("select * from answer where voteup_count>=1000 and CHAR_LENGTH(content)<=50 group by id;");
$ret=$rs->fetchAll();
if(!empty($ret)){
    $row=array_rand($ret);
    $question=$ret[$row]["question"];
    $content=$ret[$row]["content"];
    $url=$ret[$row]["url"];
}else{
    $question="「PHP 是最好的语言」这个梗是怎么来的？";
    $content="Because PHP is the best language ever, ever. It's fast, very powerful, and free.";
    $url="https://www.zhihu.com/question/26498147/answer/315847232";
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>首页 - 知乎神回复</title>
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
            .container {
                min-height:100vh;
                display: flex;
                align-items: center;
                padding-top:5rem;
                padding-bottom:5rem;
            }
            h5 {
                color: #7a8e97;
                margin-bottom:2rem;
            }
            h1 {
                font-weight: 100;
            }
            .z-brand {
                height:1.1rem;
                vertical-align: text-top;
            }
            .brand-container {
                text-align:right;
                padding-right:1rem;
                margin-top:5rem;
                margin-bottom:0;
            }
            h5,h1,p{
                opacity: 0;
                transition: 1s ease-out .0s;
            }
            .show{
                opacity: 1;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div style="width:100%;">
                <h5><i class="MDI comment-question-outline"></i> <?php echo $question; ?></h5>
                <h1><?php echo $content; ?></h1>
                <p class="brand-container">Via <a href="<?php echo $url; ?>" target="_blank"><img class="z-brand" src="https://static.1cf.co/img/Zhihu_logo.svg" alt="知乎"></a></p>
            </div>
        </div>
        <script src="https://static.1cf.co/js/jquery-3.2.1.min.js"></script>
        <script>
            window.addEventListener("load",function() {
                $("h5").addClass("show");
                setTimeout(function(){
                    $("h1").addClass("show");
                },500);
                setTimeout(function(){
                    $("p").addClass("show");
                },1000);
            },false);
        </script>
    </body>
</html>