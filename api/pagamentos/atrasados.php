<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  $cliente_id = $_SESSION['cliente_id'];

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
  
  if ($boletos_atrasados == []) {
    send([
      'status' => 404,
      'message' => 'Nenhum boleto atrasado encontrado',
    ]);
  }

  $boletos = [];
  foreach ($boletos_atrasados as $boleto) {

    //? Puxa o link de cobrança
    $venda_codigo = $boleto['descricao']; // Venda de nº XXXX
    $venda_codigo = explode(' ', $venda_codigo);
    $venda_codigo = $venda_codigo[3]; // XXXX

    $url = "vendas";
    $method = 'GET';
    $data = [
      'codigo' => $venda_codigo
    ];

    $venda = gs_click($url, $method, $data);
    $venda = $venda[0];

    $hash = $venda['hash'];
    $link_cobranca = "https://gestaoclick.com/cobranca/{$hash}";

    //? Outros dados
    $data_vencimento = $boleto['data_vencimento'];
    $valor = $boleto['valor_total'];

    $boletos[] = [
      'venda_codigo' => $venda_codigo,
      'link_cobranca' => $link_cobranca,
      'data_vencimento' => $data_vencimento,
      'valor' => $valor,
    ];

  }

  send([
    'status' => 200,
    'message' => 'Boletos atrasados encontrados',
    'boletos' => $boletos
  ]);

?>