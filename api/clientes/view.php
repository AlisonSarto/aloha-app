<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $id = $_GET['id'] ?? null;

  $url = "clientes";
  $method = 'GET';
  $data = [];
  if ($id !== null) {
    $data = [
      'id' => $id
    ];
  }

  $response = gs_click($url, $method, $data);

  $clientes = [];
  foreach ($response as $cliente) {
    $clientes[] = [
      'id' => $cliente['id'],
      'nome' => $cliente['nome'],
    ];
  }

  send([
    'status' => 200,
    'clientes' => $clientes
  ]);

?>