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
function iotget($url){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "",
	  CURLOPT_HTTPHEADER => array(
		"CK: DK7UCUHT1HB5BB0G71",
		"Content-Type: application/json",
	  ),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	$json_obj = json_decode($response);
	$value=$json_obj->{"value"}[0];
	return $value;
}
function iotpost($area,$value)
{
	$cur = curl_init();
	curl_setopt_array($cur, array(
	  CURLOPT_URL => "https://iot.cht.com.tw/iot/v1/device/17944804838/rawdata",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "[\r\n  {\r\n    \"id\": \"$area\",\r\n    \"save\": true,\r\n    \"value\": [\"$value\"]\r\n  }\r\n]",
	  CURLOPT_HTTPHEADER => array(
	    "CK: DK7UCUHT1HB5BB0G71",
	    "Content-Type: application/json",
	  ),
	));
	$response = curl_exec($cur);
	curl_close($cur);
}
include("mysql_connect.inc.php");
$access_token ='yWARnZrlhZ0gEqjA7h3kZEOIaaxTndaMIYdLh1kD/RQY0w10Jq9PH6mn5P0lKRBRsokFk7LfoUrOqii3yoERK9uldJLEEqQK0EtRHE3ug/5iNEGBkTi7+QJjIJALp2QUiC6FvMo6nkvDuU+lwsVxVgdB04t89/1O/w1cDnyilFU=';
$json_string = file_get_contents('php://input');
$json_obj = json_decode($json_string);
$event = $json_obj->{"events"}[0];
$type  = $event->{"message"}->{"type"};
$message = $event->{"message"}->{"text"};
$user_id  = $event->{"source"}->{"userId"};
$reply_token = $event->{"replyToken"};
date_default_timezone_set('Asia/Taipei');
$Time=date("Y-m-d H:i:s") ;
$value=iotget("https://iot.cht.com.tw/iot/v1/device/17944804838/sensor/A/rawdata");
$value1=iotget("https://iot.cht.com.tw/iot/v1/device/17944804838/sensor/B/rawdata");
if('012b789221' == $event->beacon->hwid && 'enter'==$event->beacon->type){
	$value++;
	iotpost("A",$value);
	$sql1 = "SELECT * FROM user where user_id = '$user_id'";
	$result1 = mysqli_query($link,$sql1);
	$row1 = mysqli_fetch_array($result1);
	if($row1['area']!='A'){
		$sql6="insert into history_list(user_id,process_area,time) values ('$user_id','A','$Time')";
		mysqli_query($link,$sql6);
		$sql8="UPDATE user set area='A' WHERE user_id = '$user_id'";
		mysqli_query($link,$sql8);
	}
	$sql="SELECT * FROM air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
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
	push($post_data,$access_token);
}
if('012b789221' == $event->beacon->hwid && 'leave'==$event->beacon->type){
	$value--;
	iotpost("A",$value);
	$time1=$Time;
	$sql="SELECT time FROM history_list WHERE user_id = '$user_id' && process_area='A' ORDER BY ID DESC LIMIT 1";
	$result = mysqli_query($link,$sql);
	$row = mysqli_fetch_array($result);
	$time2=$row[0];
	$sub=(strtotime($time1)-strtotime($time2));
	$min=$sub/60;
	$hour=$min/60;
	$realhour= intval($hour);
	$realmin=$min%60;
	$realsec=$sub%60;
	$sql6="insert into history_list(user_id,process_area,time,stay) values ('$user_id','A(leave)','$time1','$realhour：$realmin：$realsec')";
	mysqli_query($link,$sql6);
	$sql1 = "SELECT * FROM user where user_id = '$user_id'";
	$result1 = mysqli_query($link,$sql1);
	$row1 = mysqli_fetch_array($result1);
	if($row1['area']=='A'){
		$sql5="UPDATE user set area='' WHERE user_id = '$user_id'";
		mysqli_query($link,$sql5);
	}
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => "你已離開A區"
		]
	  ]
	];
	push($post_data,$access_token);
}
if('012beb3721' == $event->beacon->hwid && 'enter'==$event->beacon->type){
	$value1++;
	iotpost("B",$value1);
	$sql1 = "SELECT * FROM user where user_id = '$user_id'";
	$result1 = mysqli_query($link,$sql1);
	$row1 = mysqli_fetch_array($result1);
	if($row1['area']!='B'){
		$sql5="insert into history_list(user_id,process_area,time) values ('$user_id','B','$Time')";
		mysqli_query($link,$sql5);
		$sql8="UPDATE user set area='B' WHERE user_id = '$user_id'";
		mysqli_query($link,$sql8);
	}
	$sql="SELECT * FROM b_air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
	$result=mysqli_query($link,$sql);
	$row = mysqli_fetch_array($result);
	$replymessage='歡迎來到B區'."\n"
	.'提供您空氣品質資訊'."\n"
	.'溫度是'.(string)$row['Temperature']."°C\n"
	.'濕度是'.(string)$row['Humidity']."%\n"
	.'Co濃度是'.(string)$row['Co'];//回傳給使用者之資訊 \n要用""
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
if('012beb3721' == $event->beacon->hwid && 'leave'==$event->beacon->type){
	$value1--;
	iotpost("B",$value1);
	$time1=$Time;
	$sql="SELECT time FROM history_list WHERE user_id = '$user_id' && process_area='B' ORDER BY ID DESC LIMIT 1";
	$result = mysqli_query($link,$sql);
	$row = mysqli_fetch_array($result);
	$time2=$row[0];
	$sub=(strtotime($time1)-strtotime($time2));
	$min=$sub/60;
	$hour=$min/60;
	$realhour= intval($hour);
	$realmin=$min%60;
	$realsec=$sub%60;
	$sql6="insert into history_list(user_id,process_area,time,stay) values ('$user_id','B(leave)','$time1','$realhour:$realmin:$realsec')";
	mysqli_query($link,$sql6);
	$sql2 = "SELECT * FROM user where user_id = '$user_id'";
	$result2 = mysqli_query($link,$sql2);
	$row2 = mysqli_fetch_array($result2);
	if($row2['area']=='B'){
		$sql5="UPDATE user set area='' WHERE user_id = '$user_id'";
		mysqli_query($link,$sql5);
	}
	$post_data = [
	  "replyToken" => $reply_token,
	  "messages" => [
		[
		  "type" => "text",
		  "text" => "你已離開B區"
		]
	  ]
	];
	push($post_data,$access_token);
}
if($type == "text"){
	
	$sql="insert into user(user_id) values ('$user_id')";
	mysqli_query($link,$sql);
	
	$sql9 = "SELECT * FROM user where user_id = '$user_id'";
	$result2 = mysqli_query($link,$sql9);
	$row = mysqli_fetch_array($result2);
	
	if($row['user_name']==NULL)
	{
		if(substr($message,0,7)=="姓名@")
		{
			$name=substr($message,7);
			$sql="UPDATE user set user_name='$name' where user_id='$user_id'";
			mysqli_query($link,$sql);
			//$sql8="UPDATE user set area='A' WHERE user_id = '$user_id'";
			//mysqli_query($link,$sql8);
			$post_data = [
			  "replyToken" => $reply_token,
			  "messages" => [
				[
				  "type" => "text",
				  "text" =>  "你好 $name"."\n"."你可以使用其他功能了"
				]
			  ]
			];
			push($post_data,$access_token);
		}
		else
		{
			$post_data = [
			  "replyToken" => $reply_token,
			  "messages" => [
				[
				  "type" => "text",
				  //"text" => "你好 $message \n哈哈 $message" ,
				  "text" =>  "姓名格式輸入錯誤喔"."\n"."格式為:姓名@xxx"
				]
			  ]
			];
			push($post_data,$access_token);
		}
		
	}
	else
	{
		switch ($message)
		{
			  case "@空氣品質":
				switch ($row['area'])
				{
					case "A":
						$sql="SELECT * FROM air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
						$result=mysqli_query($link,$sql);
						$row = mysqli_fetch_array($result);
						$replymessage='您所在A區'."\n"
						.'溫度是'.(string)$row['Temperature']."°C\n"
						.'濕度是'.(string)$row['Humidity']."%\n"
						.'Co濃度是'.(string)$row['Co']."\n"
						.'Co2濃度是'.(string)$row['Co2']."PPM\n"	
						.'PM2.5是'.(string)$row['PM25'];//回傳給使用者之資訊 \n要用""
						break;
					case "B":
						$sql="SELECT * FROM b_air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
						$result=mysqli_query($link,$sql);
						$row = mysqli_fetch_array($result);
						$replymessage='您所在B區'."\n"
						.'溫度是'.(string)$row['Temperature']."°C\n"
						.'濕度是'.(string)$row['Humidity']."%\n"
						.'Co濃度是'.(string)$row['Co'];//回傳給使用者之資訊 \n要用""
						break;
					case "C":
						$sql="SELECT * FROM c_air_information ORDER BY ID DESC LIMIT 1";//選擇最新的空氣資訊
						$result=mysqli_query($link,$sql);
						$row = mysqli_fetch_array($result);
						break;
					
				}
				
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
				break;
			  case "@關閉提醒":
				$sql="UPDATE close_reminder set reminder=0 where only=1";//選擇最新的空氣資訊
				$result=mysqli_query($link,$sql);
				$replymessage='已關閉提醒5分鐘';//回傳給使用者之資訊 \n要用""
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
				break;
		} 
	}
}
?>
