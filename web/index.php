<?php
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
include("mysql_connect.inc.php");
$access_token ='ylC4bIeHjRfORJkjFIO+Nr5xJmVcyRT0wTBfTK39y4MrMfyrCpVPrh17C5L7B+Qi+7FvCQdPgwnhiwUjY+/zdZ1Q2WTJeuI5lZzXF7TCgCe3spxowyQmi7chHySTW0cVB1f9E1EYn8o+HTrndV3NsAdB04t89/1O/w1cDnyilFU=';
$json_string = file_get_contents('php://input');
$json_obj = json_decode($json_string);
$event = $json_obj->{"events"}[0];
$type  = $event->{"message"}->{"type"};
$message = $event->{"message"}->{"text"};
$user_id  = $event->{"source"}->{"userId"};
$reply_token = $event->{"replyToken"};
date_default_timezone_set('Asia/Taipei');
if($type == "text"){
	$sql="SELECT * FROM air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
	$result=mysqli_query($link,$sql);
	if (!$result){
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => '連線錯誤'
		]
	  ]
	];
	push($post_data,$access_token);	
	}
	$row = mysqli_fetch_array($result);
	$replymessage='歡迎！'."\n"
	.'提供您空氣品質資訊'."\n"
	.'溫度是'.(string)$row['Temperature']."°C\n"
	.'濕度是'.(string)$row['Humidity']."%\n"
	.'Co濃度是'.(string)$row['Co']."\n";//回傳給使用者之資訊 \n要用""
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => $replymessage
		]
	  ]
	];
    	push($post_data,$access_token);
}
if('enter'==$event->beacon->type){ //'013e6460ee' == $event->beacon->hwid && 'enter'==$event->beacon->type
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => 'Welcome!'
		]
	  ]
	];
    	push($post_data,$access_token);
	/*$sql="SELECT * FROM air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
	$result=mysqli_query($link,$sql);
	$row = mysqli_fetch_array($result);
	$replymessage='歡迎來到A區'."\n"
	.'提供您空氣品質資訊'."\n"
	.'溫度是'.(string)$row['Temperature']."°C\n"
	.'濕度是'.(string)$row['Humidity']."%\n"
	.'Co濃度是'.(string)$row['Co']."\n"
	.'Co2濃度是'.(string)$row['Co2']."PPM\n"	
	.'PM2.5是'.(string)$row['PM25'];//回傳給使用者之資訊 \n要用""
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => $replymessage
		]
	  ]
	];
	push($post_data,$access_token);*/
}

?>
