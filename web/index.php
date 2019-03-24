<?php
include("mysql_connect.inc.php");
//$sql="insert into user(user_id) values ('$user_id')";
//mysqli_query($link,$sql);
$access_token ='yWARnZrlhZ0gEqjA7h3kZEOIaaxTndaMIYdLh1kD/RQY0w10Jq9PH6mn5P0lKRBRsokFk7LfoUrOqii3yoERK9uldJLEEqQK0EtRHE3ug/5iNEGBkTi7+QJjIJALp2QUiC6FvMo6nkvDuU+lwsVxVgdB04t89/1O/w1cDnyilFU=';
//$file = fopen("D:\\Line_log.txt", "a+");
//fwrite($file, $json_string."\n"); 
$sql="SELECT * FROM test3 ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
$result=mysqli_query($link,$sql);
$row = mysqli_fetch_array($result);
$message='現在的溫度是'.(string)$row['Temperature']."°C\n"
  .'濕度是'.(string)$row['Humidity']."%\n"
  .'Co濃度是'.(string)$row['Co']."%\n"
  .'PM2.5是'.(string)$row['PM25'];//回傳給使用者之資訊 \n要用""

$json_string = file_get_contents('php://input');
$json_obj = json_decode($json_string);
$event = $json_obj->{"events"}[0];
$type  = $event->{"message"}->{"type"};
$replymessage = $event->{"message"}->{"text"};//抓取使用者輸入訊息判斷
$user_id  = $event->{"source"}->{"userId"};
$reply_token = $event->{"replyToken"};
$post_data=[];//LINE 接收 jason
switch ($replymessage)
{
  case "@空氣品質":
    $post_data = [
      "replyToken" => $reply_token,
      "messages" => [
        [
          "type" => "text",
          "text" => $message
        ]
      ]
    ]; 
    break;
  //fwrite($file, json_encode($post_data)."\n");
} 

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
  $resultfinal = curl_exec($ch);
 //fwrite($file, $result."\n");  
  //fclose($file);
  curl_close($ch); 
   
?>
