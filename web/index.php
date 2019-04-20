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
	}
	if($message=="查詢廁所已使用人數")
	{
		$sql=="SELECT count FROM Cleaning_count where area='A'";
		$result=mysqli_query($link,$sql);
		$row =mysqli_fetch_array($result);
	
		$post_data = [
		  "replyToken" => $reply_token,
		  "messages" => [
			[
			  "type" => "text",
			  "text" => "現已進入 echo $row[0]人"
			]
		  ]
		];
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
