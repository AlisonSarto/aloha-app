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

  //* Pedidos Aloha
  $url = 'vendas';
  $method = 'GET';
  $data = [
    'situacao_id[]' => 4629853, //? Aberto
    'situacao_id[]' => 4737015 //? Em rota
  ];

  $response = gs_click($url, $method, $data);

  $vendas_aloha = [];

  foreach ($response as $venda) {
    $entrega = $venda['prazo_entrega'];
    $entrega = date('d/m/Y', strtotime($entrega));

    if ($venda['situacao_id'] == 4629853) {
      $situacao = '🟠 '. $venda['nome_situacao'];
    } else {
      $situacao = '🔵 '. $venda['nome_situacao'];
    }

    $vendas_aloha[] = [
      'numero' => $venda['codigo'],
      'nome_cliente' => $venda['nome_cliente'],
      'prazo_entrega' => $entrega,
      'situacao' => $situacao,
    ];
  }

  //* Pedidos Fábrica
  $url = 'vendas';
  $method = 'GET';
  $data = [
    'situacao_id[]' => 4629853, //? Aberto
    'situacao_id[]' => 4737015 //? Em rota
  ];

  $response = gs_click($url, $method, $data);

  $vendas_fabrica = [];

  foreach ($response as $venda) {
    $entrega = $venda['prazo_entrega'];
    $entrega = date('d/m/Y', strtotime($entrega));

    if ($venda['situacao_id'] == 4629853) {
      $situacao = '🟠 '. $venda['nome_situacao'];
    } else {
      $situacao = '🔵 '. $venda['nome_situacao'];
    }

    $vendas_fabrica[] = [
      'numero' => $venda['codigo'],
      'nome_cliente' => $venda['nome_cliente'],
      'prazo_entrega' => $entrega,
      'situacao' => $situacao,
    ];
  }

  $mensagem = "";
  
  if (!empty($vendas)) {

    if (!empty($vendas_aloha)) {
      $mensagem .= "🥥 *Pedidos Aloha*\n\n";
  
      foreach ($vendas_aloha as $venda) {
        $numero = $venda['numero'];
        $nome_cliente = $venda['nome_cliente'];
        $prazo_entrega = $venda['prazo_entrega'];
        $situacao = $venda['situacao'];
    
        $mensagem .= "📦 *Nº:* $numero\n🏪 *Cliente:* $nome_cliente\n📅 *Entrega:* $prazo_entrega\n📊 *Situação:* $situacao\n\n";
      }
    }

    if (!empty($vendas_fabrica)) {

      if (!empty($vendas_aloha)) {
        $mensagem .= "\n\n";
      }

      $mensagem .= "🏭 *Pedidos Fábrica*\n\n";

      foreach ($vendas_fabrica as $venda) {
        $numero = $venda['numero'];
        $nome_cliente = $venda['nome_cliente'];
        $prazo_entrega = $venda['prazo_entrega'];
        $situacao = $venda['situacao'];
    
        $mensagem .= "📦 *Nº:* $numero\n🏪 *Cliente:* $nome_cliente\n📅 *Entrega:* $prazo_entrega\n📊 *Situação:* $situacao\n\n";
      }
    }
  
  } else {
    $mensagem = 'Nenhum pedido encontrado';
  }
  
  send([
    'status' => 200,
    'message' => $mensagem
  ]);
  
?>