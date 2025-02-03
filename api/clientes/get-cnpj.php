<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $cnpj = file_get_contents('php://input');
  $cnpj = json_decode($cnpj, true);
  $cnpj = $cnpj['cnpj'] ?? null;

  if ($cnpj == null) {
    send([
      'status' => 400,
      'message' => 'CNPJ não informado',
    ]);
  }

  //remove os . e / e - do cnpj
  $cnpj = str_replace('.', '', $cnpj);
  $cnpj = str_replace('/', '', $cnpj);
  $cnpj = str_replace('-', '', $cnpj);

  $url = "https://publica.cnpj.ws/cnpj/$cnpj";
  $method = 'GET';
  $data = [];
  $header = [];

  $response = curl($url, $header, $method, $data);

  $ps_err = $response['detalhes'] ?? null;
  if ($ps_err == "CNPJ inválido") {
    send([
      'status' => 400,
      'message' => 'CNPJ inválido'
    ]);
  }

  $razao_social = $response['razao_social'];
  $cep = $response['estabelecimento']['cep'];
  $endereço = $response['estabelecimento']['tipo_logradouro'] . ' ' . $response['estabelecimento']['logradouro'] . ', N º' . $response['estabelecimento']['numero'];

  send([
    'status' => 200,
    'razao_social' => $razao_social,
    'cep' => $cep,
    'endereço' => $endereço,
  ]);

?>