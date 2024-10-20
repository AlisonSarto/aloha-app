<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  $cliente_id = $_SESSION['cliente_id'];

  //? Profile gestão click
  $url = "clientes";
  $method = 'GET';
  $data = [
    'id' => $cliente_id
  ];
  $response = gs_click($url, $method, $data);

  //? Profile interno
  $sql = "SELECT id, cliente_id, email, vlr_pacote, vlr_frete FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows === 0) {
    $db = [];
  }else {
    $db = $res->fetch_all(MYSQLI_ASSOC);
  }


  send([
    'status' => 200,
    'session' => $_SESSION,
    'profile_interno' => $db,
    'profile' => $response
  ]);

?>