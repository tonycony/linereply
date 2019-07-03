<?php
include("mysql_connect.inc.php");
$access_token ='IOLzhvJfIAaQgH3xi7ppOr+spSkkHIXQ4MJNeRDaYA9+s+oQNqtRc5zp49lfFSWBGjsErF/pj1M1SWjnsCass2BfuhGBajbYq1xLyxh53d5lJJNDnWq8nWl7tp6JyBCZMtRJ6xMjGAKnZxkQkPqg1AdB04t89/1O/w1cDnyilFU=';
//define('TOKEN', '你的Channel Access Token');
$json_obj = json_decode(file_get_contents('php://input'));
$event = $json_obj->{"events"}[0];
$type  = $event->{"message"}->{"type"};
$message = $event->{"message"}->{"text"};
$user_id  = $event->{"source"}->{"userId"};
$reply_token = $event->{"replyToken"};
$obj_id = $event->{"message"}->{"id"};
if($type == "text"){
	$sql="insert into Cleaning_staff(user_id) values ('$user_id')";
	mysqli_query($link,$sql);
	
	$sql9 = "SELECT * FROM Cleaning_staff where user_id= '$user_id'";
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
		push($post_data,$access_token);
	}
	if(substr($message,0,9)=="姓名：")
	{
		$name=substr($message,9);
		$sql="UPDATE Cleaning_staff set user_name='$name' where user_id='$user_id'";
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
		push($post_data,$access_token);
	}
	if(substr($message,0,7)=="姓名:")
	{
		$name=substr($message,7);
		$sql="UPDATE Cleaning_staff set user_name='$name' where user_id='$user_id'";
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
		push($post_data,$access_token);
	}
	if($message=="查詢廁所已使用人數")
	{
		$sql="SELECT count FROM Cleaning_count where area='A'";
		$result=mysqli_query($link,$sql);
		$row =mysqli_fetch_array($result);
	
		$post_data = [
		  "replyToken" => $reply_token,
		  "messages" => [
			[
			  "type" => "text",
			  "text" => '現已進入 '.(string)$row[0].' 人'
			]
		  ]
		];
		push($post_data,$access_token);
	}
	if($message=="重新計數")
	{
		$sql="UPDATE Cleaning_count set count=0 where area='A'";
		mysqli_query($link,$sql);
		
		$post_data = [
		  "replyToken" => $reply_token,
		  "messages" => [
			[
			  "type" => "text",
			  "text" =>  "已重新計數"
			]
		  ]
		];
		push($post_data,$access_token);
	}
}
if($type == "image"){
	get($obj_id);
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => $msg
		]
	  ]
	];
	push($post_data,$access_token);
	
}
function get($obj_id){
	$ch = curl_init("https://api.line.me/v2/bot/message/".$obj_id."/content");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Authorization: Bearer '.$access_token
	));
	$result = curl_exec($ch);
	curl_close($ch);
	$msg   = json_decode($result);
}
function push($post_data,$access_token)
{
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
	curl_close($ch); 
}
?>
