<?php
  define("DB_SERVER", "52.1.151.129");
  define("DB_USERNAME", "case");
  define("DB_PASSWORD", "1234");
  define("DB_DATABASE", "linebot");

  $link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
  // you could test connection eventually using a if and else conditional statement, 
  // feel free to take out the code below once you see Connected!
  //mysqli_query($link,'SET CHARACTER SET utf8');
  //mysqli_query($link,"SET collation_connection = 'utf8_general_ci'");
  if ($link) {
    echo "Connected!";
  } else {
    echo "Connection Failed";
  }
?>
