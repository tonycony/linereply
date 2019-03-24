<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
//資料庫設定
//資料庫位置
$db_server = "54.156.124.2";
//資料庫名稱
$db_name = "esp8266";
//資料庫管理者帳號
$db_user = "case";
//資料庫管理者密碼
$db_passwd = "1234";

//對資料庫連線
$link=mysqli_connect($db_server, $db_user, $db_passwd)
       or die("無法對資料庫連線");

//資料庫連線採UTF8
mysqli_query($link,'SET CHARACTER SET utf8');
mysqli_query($link,"SET collation_connection = 'utf8_general_ci'");

//選擇資料庫
mysqli_select_db($link,$db_name)
      or die("無法使用資料庫");
?>  
