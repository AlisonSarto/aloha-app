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

  $entregador = $json['entregador'] ?? null;
  $codigo = $json['codigo'] ?? null;
  $situacao = $json['situacao'] ?? null;
  $empresa_id = $json['empresa_id'] ?? null;

  if ($codigo === null || $situacao === null || $empresa_id === null || $entregador === null) {
    send([
      'status' => 400,
      'error' => 'Parâmetros insuficientes'
    ]);
  }

  $url = 'vendas';
  $method = 'GET';
  $data = [
    'loja_id' => $empresa_id,
    'codigo' => $codigo
  ];

  $response = gs_click($url, $method, $data);

  $id_venda = $response[0]['id'] ?? null;

  if ($id_venda === null) {
    send([
      'status' => 404,
      'error' => 'Venda não encontrada'
    ]);
  }

  $cliente_id = $response[0]['cliente_id'];
  $pagamentos = $response[0]['pagamentos'];
  $valor_frete = $response[0]['valor_frete'] == 0 ? 0.000000000000000001 : $response[0]['valor_frete'];
  $valor_total = $response[0]['valor_total'];
  $condicao_pagamento = $response[0]['condicao_pagamento'];
  $data = $response[0]['data'];
  $produtos = $response[0]['produtos'];
  $observacoes = $response[0]['observacoes'];
  if ($situacao == 2) {
    if ($observacoes != "") {
      $observacoes = "$observacoes\n\nEntregador: $entregador";
    }else {
      $observacoes = "Entregador: $entregador";
    }
  }

  if ($situacao == 1) {
    $situacao = 7680727; //? Reimprimir
  }else if ($situacao == 2) {
    $situacao = 4737015; //? Em rota
  } else if ($situacao == 3) {
    $situacao = 3395254;  //? Entregue
  }

  $url = "vendas/$id_venda";
  $method = 'PUT';
  $data = [
    'tipo' => 'produto',
    'loja_id' => $empresa_id,
    'codigo' => $codigo,
    'cliente_id' => $cliente_id,
    'situacao_id' => $situacao,
    'data' => $data,
    'condicao_pagamento' => $condicao_pagamento,
    'valor_frete' => $valor_frete,
    'valor_total' => $valor_total,
    "pagamentos" => $pagamentos,
    'produtos' => $produtos,
    'observacoes' => $observacoes
  ];

  $response = gs_click($url, $method, $data);

  send([
    'status' => 200,
    'response' => $response
  ]);
  
?>