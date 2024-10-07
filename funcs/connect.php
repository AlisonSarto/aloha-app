<?php

  include $_SERVER['DOCUMENT_ROOT'].'/server/funcs/env.php';

  date_default_timezone_set('Etc/GMT+3');

  $dbHost = env('DB_HOST');
  $dbUsername = env('DB_USER');
  $dbPassword = env('DB_PASS');
  $dbName = env('DB_NAME');

  $conn = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

  include $_SERVER['DOCUMENT_ROOT'].'/server/funcs/logs.php';
  include $_SERVER['DOCUMENT_ROOT'].'/server/funcs/curl.php';

?>