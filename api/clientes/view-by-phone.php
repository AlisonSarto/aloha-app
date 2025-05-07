<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $phone = file_get_contents('php://input');
  $phone = json_decode($phone, true);
  $phone = $phone['phone'] ?? null;

  if ($phone == null) {
    send([
      'status' => 400,
      'message' => 'Telefone não informado',
    ]);
  }


  $phone = str_replace('+', '', $phone);
  if (substr($phone, 0, 2) == '55') {
    $phone = substr($phone, 2);
  }


  $url = "clientes";
  $method = 'GET';
  $data = [];
  $data = [
    'telefone' => $phone
  ];

  $response = gs_click($url, $method, $data);

  if ($response == "Nenhum cliente foi encontrado!" || $response == []) {
    send([
      'status' => 400,
      'message' => 'Cliente não encontrado'
    ]);
  }

  $cliente_id = $response[0]['id'];
  $cliente_id = base64_encode($cliente_id);

  $magic = "https://www.alohaaapp.com.br/api/login/magic?access=$cliente_id";

  send([
    'status' => 200,
    'link' => $magic,
    'phone' => $phone
  ]);

?>