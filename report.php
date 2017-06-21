<?php
session_start();

require './library/emotion.php';
require './library/upload.php';

$personGroupId = 'offsite-demo';
// save your uploaded photo
$imgUrl = Upload::save($_FILES['photo']);

// use Azure's api to detect face
$requestBody = <<<EOF
{
    "url":"{$imgUrl}"
}
EOF;
$rs = Azure::POST('https://api.cognitive.azure.cn/emotion/v1.0/recognize', $requestBody);
error_log(var_export($rs, true));
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Olay</title>
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="yes" name="apple-touch-fullscreen">
    <meta content="telephone=no,email=no" name="format-detection">
    <meta name="App-Config" content="fullscreen=yes,useHistoryState=yes,transition=yes">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link href="./assets/style.css" rel="stylesheet" type="text/css">
    <link href="./assets/report.css" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div class="container report">
        <div class="report-head">
            <div class="age-presenter">
                <h4>Photo you've uploaded</h4>
                <img class="uploaded" src='<?php echo $imgUrl;?>' alt='' />
            </div>

            <?php
            $msg = '';
            switch ($rs['resultCode']) {
                case 400:
                    $msg = $rs['response'];
                    break;
            }
            ?>
            <?php
            if ($msg !== '') {
            ?>
            <p class="text text-view intro"><?php echo $msg;?></p>  
            <?php
            } else {
            ?>
            <p class="text text-view intro" style='color: #000'>
                <?php
                $data = $rs[0];
                ?>

                Emotion: <br />
                <?php
                $scores = (array)$data['scores'];
                $max = 0;
                $emotion = '';
                foreach ($scores as $key => $val) {
                    $val = (float)$val;
                    if ($val >= $max) {
                        $max = $val;
                        $emotion = $key;
                    }
                }
                ?>
                <?php echo ucfirst($emotion);?>
            </p> 
            <?php
            }
            ?>
        </div>

        <div class="buttons">
            <button class="myproducts" onclick="window.location='index.php';">Retake Analysis</button>
        </div>
    </div>
 
  </body>
</html>
