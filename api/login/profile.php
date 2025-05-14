<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  $cliente_id = $_SESSION['cliente_id'];

  //? Profile gestão click
  $url = "clientes";
  $method = 'GET';
  $data = [
    'id' => $cliente_id
  ];
  $profile_gestao = gs_click($url, $method, $data);

  //? Se ele está com boleto atrasado
  $url = "recebimentos";
  $method = 'GET';
  $data = [
    'data_inicio' => "2023-01-01",
    'cliente_id' => $cliente_id,
    'liquidado' => 'at',
    'forma_pagamento_id' => 2219792 // Boleto
  ];

  $boletos_atrasados = gs_click($url, $method, $data);
  
  $devendo = true;
  if ($boletos_atrasados == []) {
    $devendo = false;
  }

  //? Profile interno
  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
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
    'profile' => $profile_gestao,
    'boleto_atrasado' => $devendo,
  ]);

?>