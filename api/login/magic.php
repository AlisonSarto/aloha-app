<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $access = $_GET['access'] ?? null;

  if ($access === null) {
    send([
      'status' => 400,
      'message' => 'access não informado'
    ]);
  }

  //? Decodifica
  $cliente_id = base64_decode($access);

  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows > 0) {
    header('Location: /login');    
  }

  session_unset();

  //? Verifica se o cliente existe
  $url = "clientes";
  $method = 'GET';
  $data = [
    'id' => $cliente_id
  ];

  $response = gs_click($url, $method, $data);

  if ($response == []) {
    header('Location: /login');
  }

  $_SESSION['cliente_id'] = $cliente_id;
  header('Location: /app/registro');

?>