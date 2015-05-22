<?php
header('Content-Type: text/html; charset=UTF-8');
define("APPID", "wx6fa4388ad0b35f13");
define("APPSECRET", "2a14f917c4ace5b21a7a217583cbf921");
$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
$json=file_get_contents($TOKEN_URL);
$result=json_decode($json);
$ACC_TOKEN=$result->access_token;
echo "$ACC_TOKEN"
?>
