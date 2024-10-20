<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $id = $_GET['id'] ?? null;

  $data_inicio = '2020-01-01';

  $url = "recebimentos";
  $method = 'GET';
  $data = [
    'cliente_id' => $_SESSION['cliente_id'],
    'limit' => 9999,
    'liquidado' => 'ab',
    'data_inicio' => $data_inicio,
  ];
  if ($id !== null) {
    $data = [
      'id' => $id
    ];
  }

  $response = gs_click($url, $method, $data);

  send([
    'status' => 200,
    'financeiro' => $response
  ]);

?>