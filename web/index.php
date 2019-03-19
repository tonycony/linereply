<?php
$access_token ='yWARnZrlhZ0gEqjA7h3kZEOIaaxTndaMIYdLh1kD/RQY0w10Jq9PH6mn5P0lKRBRsokFk7LfoUrOqii3yoERK9uldJLEEqQK0EtRHE3ug/5iNEGBkTi7+QJjIJALp2QUiC6FvMo6nkvDuU+lwsVxVgdB04t89/1O/w1cDnyilFU=';
//define('TOKEN', '你的Channel Access Token');

$json_string = file_get_contents('php://input');

$file = fopen("D:\\Line_log.txt", "a+");
fwrite($file, $json_string."\n"); 
$json_obj = json_decode($json_string);
$event = $json_obj->{"events"}[0];
$type  = $event->{"message"}->{"type"};
$message = $event->{"message"};
$user_id  = $event->{"source"}->{"userId"};

$reply_token = $event->{"replyToken"};
$post_data = [
  "replyToken" => $reply_token,
  "messages" => [
    [
      "type" => "text",
      "text" => $message->{"text"}
    ]
  ]
]; 
fwrite($file, json_encode($post_data)."\n");

$ch = curl_init("https://api.line.me/v2/bot/message/reply");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$access_token
    //'Authorization: Bearer '. TOKEN
));
$result = curl_exec($ch);
fwrite($file, $result."\n");  
fclose($file);
curl_close($ch); 
include("mysql_connect.inc.php");
$sql="insert into user(user_id) values ('$user_id')";
mysqli_query($link,$sql);
?>
