<?php

  $expireTime = 604800;
  session_set_cookie_params($expireTime);
  session_start();

  // include $_SERVER['DOCUMENT_ROOT'] . '/funcs/env.php';
  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/gs_click.php';
  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/send.php';

  date_default_timezone_set('Etc/GMT+3');

  $dbHost = env('DB_HOST');
  $dbUsername = env('DB_USER');
  $dbPassword = env('DB_PASS');
  $dbName = env('DB_NAME');

  $conn = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

?>