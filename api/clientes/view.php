<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $id = $_GET['id'] ?? null;

  $url = "https://api.beteltecnologia.com/clientes";
  $header = [
    "access-token: $gs_access_token",
    "secret-access-token: $gs_secret_token"
  ];
  $method = 'GET';

  $data = [];
  if ($id !== null) {
    $data = [
      'id' => $id
    ];
  }

  $response = curl($url, $header, $method, $data);
  $n_paginas = $response['meta']['total_paginas'];
  
  $clientes_gc = $response['data'];

  $n = 2;
  if ($n_paginas > 1) {

    $url = "https://api.beteltecnologia.com/clientes";
    $header = [
      "access-token: $gs_access_token",
      "secret-access-token: $gs_secret_token"
    ];
    $method = 'GET';
    $data = [
      'pagina' => $n
    ];
  
    $response = curl($url, $header, $method);
    $clientes_gc = array_merge($clientes_gc, $response['data']);

  }

  $clientes = [];
  foreach ($clientes_gc as $cliente) {
    $clientes[] = [
      'id' => $cliente['id'],
      'nome' => $cliente['nome'],
      'cnpj' => $cliente['cnpj'],
      'cpf' => $cliente['cpf'],
      'login_link' => '/'
    ];
  }

  send([
    'status' => 200,
    'clientes' => $clientes
  ]);

?>