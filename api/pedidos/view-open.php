<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  //? Exclusivo botconversa
  $json = file_get_contents('php://input');
  $json = json_decode($json, true);
  
  $token = $json['token'] ?? null;

  if ($token !== 'botconversa') {
    send([
      'status' => 401,
      'error' => 'Não autorizado'
    ]);
  }

  //* Pedidos abertos
  $url = 'vendas';
  $method = 'GET';
  $data = [
    'situacao_id' => 4629853 //? Aberto
  ];

  $response = gs_click($url, $method, $data);

  $vendas = [];

  foreach ($response as $venda) {
    $entrega = $venda['prazo_entrega'];
    $entrega = date('d/m/Y', strtotime($entrega));

    $situacao = '🟠 '. $venda['nome_situacao'];

    $vendas[] = [
      'numero' => $venda['codigo'],
      'nome_cliente' => $venda['nome_cliente'],
      'prazo_entrega' => $entrega,
      'situacao' => $situacao,
    ];
  }

  //* Pedidos em rota
  $url = 'vendas';
  $method = 'GET';
  $data = [
    'situacao_id' => 4737015 //? Em rota
  ];

  $response = gs_click($url, $method, $data);

  foreach ($response as $venda) {
    $entrega = $venda['prazo_entrega'];
    $entrega = date('d/m/Y', strtotime($entrega));

    $situacao = '🔵 '. $venda['nome_situacao'];

    $vendas[] = [
      'numero' => $venda['codigo'],
      'nome_cliente' => $venda['nome_cliente'],
      'prazo_entrega' => $entrega,
      'situacao' => $situacao,
    ];
  }

  $mensagem = "";
  
  if (!empty($vendas)) {
  
    foreach ($vendas as $venda) {
      $numero = $venda['numero'];
      $nome_cliente = $venda['nome_cliente'];
      $prazo_entrega = $venda['prazo_entrega'];
      $situacao = $venda['situacao'];
  
      $mensagem .= "
        📦 *Nº:* $numero\n🏪 *Cliente:* $nome_cliente\n📅 *Entrega:* $prazo_entrega\n📊 *Situação:* $situacao\n
        \n
      ";
    }
  
  } else {
    $mensagem = 'Nenhum pedido encontrado';
  }
  
  send([
    'status' => 200,
    'message' => $mensagem
  ]);
  
?>