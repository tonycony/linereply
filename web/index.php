<?php
$access_token ='IOLzhvJfIAaQgH3xi7ppOr+spSkkHIXQ4MJNeRDaYA9+s+oQNqtRc5zp49lfFSWBGjsErF/pj1M1SWjnsCass2BfuhGBajbYq1xLyxh53d5lJJNDnWq8nWl7tp6JyBCZMtRJ6xMjGAKnZxkQkPqg1AdB04t89/1O/w1cDnyilFU=';
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
      "text" => $message
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