<?php
include("mysql_connect.inc.php");
$access_token ='IOLzhvJfIAaQgH3xi7ppOr+spSkkHIXQ4MJNeRDaYA9+s+oQNqtRc5zp49lfFSWBGjsErF/pj1M1SWjnsCass2BfuhGBajbYq1xLyxh53d5lJJNDnWq8nWl7tp6JyBCZMtRJ6xMjGAKnZxkQkPqg1AdB04t89/1O/w1cDnyilFU=';
//define('TOKEN', '你的Channel Access Token');

$json_string = file_get_contents('php://input');

//$file = fopen("D:\\Line_log.txt", "a+");
//fwrite($file, $json_string."\n"); 
$json_obj = json_decode($json_string);
$event = $json_obj->{"events"}[0];
$type  = $event->{"message"}->{"type"};
$message = $event->{"message"}->{"text"};
$user_id  = $event->{"source"}->{"userId"};

/*$code = '100058';
$bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);
$emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');*/

$reply_token = $event->{"replyToken"};
if($type == "text"){
	$sql="insert into user(user_id) values ('$user_id')";
	mysqli_query($link,$sql);
	
	$sql9 = "SELECT * FROM user where user_id= '$user_id'";
	$result2 = mysqli_query($link,$sql9);
	$row = mysqli_fetch_row($result2);
	
	if($row[1]==NULL)
	{
		$post_data = [
		  "replyToken" => $reply_token,
		  "messages" => [
			[
			  "type" => "text",
			  "text" =>  "請先輸入您的姓名\n以利為您服務喔\n輸入格式為 (姓名：xxx)"
			]
		  ]
		];
	}
	if(substr($message,0,9)=="姓名：")
	{
		$name=substr($message,9);
		$sql="UPDATE user set user_name='$name' where user_id='$user_id'";
		mysqli_query($link,$sql);
		$post_data = [
		  "replyToken" => $reply_token,
		  'messages' => array(
				array(
					'type' => 'template', // 訊息類型 (模板)
					'altText' => 'Example confirm template', // 替代文字
					'template' => array(
						'type' => 'confirm', // 類型 (確認)
						'text' => 'Are you sure?', // 文字
						'actions' => array(
							array(
								'type' => 'message', // 類型 (訊息)
								'label' => 'Yes', // 標籤 1
								'text' => 'Yes' // 用戶發送文字 1
							),
							array(
								'type' => 'message', // 類型 (訊息)
								'label' => 'No', // 標籤 2
								'text' => 'No' // 用戶發送文字 2
							)
						)
					)
				)
			)
		];
		$message1 = $event->{"template"}->{"actions"}->{"text"};
		if($message1=="Yes"){
			$name=substr($message,9);
			$post_data = [
			  "replyToken" => $reply_token,
			  "messages" => [
				[
				  "type" => "text",
				  "text" =>  "你好 $name"
				]
			  ]
			];
		}
	}
	if(substr($message,0,7)=="姓名:")
	{
		$name=substr($message,7);
		$sql="UPDATE user set user_name='$name' where user_id='$user_id'";
		mysqli_query($link,$sql);
		$post_data = [
		  "replyToken" => $reply_token,
		  "messages" => [
			[
			  "type" => "text",
			  "text" =>  "你好 $name"
			]
		  ]
		];
	}
}
//fwrite($file, json_encode($post_data)."\n");

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
//fwrite($file, $result."\n");  
//fclose($file);
curl_close($ch); 
//include("mysql_connect.inc.php");
//$sql="insert into user(user_id, user_name) values ('$user_id', '$message')";
//mysqli_query($link,$sql);
?>