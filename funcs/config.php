<?php

  $expireTime = 604800;
  session_set_cookie_params($expireTime);
  session_start();

  // include $_SERVER['DOCUMENT_ROOT'] . '/funcs/env.php';
  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/gs_click.php';
  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/send.php';

  function prazoEntrega($dia_atual, $prazo) {
    $dataAtual = new DateTime($dia_atual);
    $diasAdicionados = 0;

    while ($diasAdicionados < $prazo) {
      $dataAtual->modify('+1 day');
      
      // Verifica se é domingo
      if ($dataAtual->format('w') != 0) {
        $diasAdicionados++;
      }
    }

    return $dataAtual;
  }

  date_default_timezone_set('Etc/GMT+3');

  $dbHost = env('DB_HOST');
  $dbUsername = env('DB_USER');
  $dbPassword = env('DB_PASS');
  $dbName = env('DB_NAME');

  $conn = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

  $dbHost = env('DB_HOST_CRM');
  $dbUsername = env('DB_USER_CRM');
  $dbPassword = env('DB_PASS_CRM');
  $dbName = env('DB_NAME_CRM');

  $conn_crm = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

?>