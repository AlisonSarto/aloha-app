<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $cliente_id = $_GET['cliente_id'] ?? null;

  if ($cliente_id === null) {
    send([
      'status' => 400,
      'message' => 'Cliente ID não informado'
    ]);
  }

  $sql = "SELECT * FROM users WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows > 0) {
    header('Location: /login');    
  }

  //? Verifica se o cliente existe
  $url = "https://api.beteltecnologia.com/clientes";
  $header = [
    "access-token: $gs_access_token",
    "secret-access-token: $gs_secret_token"
  ];
  $method = 'GET';
  $data = [
    'id' => $cliente_id
  ];

  $response = curl($url, $header, $method, $data);

  if ($response['data'] == 'Nenhum cliente foi encontrado!') {
    send([
      'status' => 405,
      'message' => 'Acesso negado'
    ]);
  }

  send([
    'status' => 200,
    'cliente' => $response['data']
  ]);

  session_destroy();
  $_SESSION['cliente_id'] = $cliente_id;
  header('Location: /login');

?>