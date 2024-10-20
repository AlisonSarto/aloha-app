<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  $cliente_id = $_SESSION['cliente_id'];

  $url = "clientes";
  $method = 'GET';
  $data = [
    'id' => $cliente_id
  ];

  $response = gs_click($url, $method, $data);

  send([
    'status' => 200,
    'profile' => $response
  ]);

?>