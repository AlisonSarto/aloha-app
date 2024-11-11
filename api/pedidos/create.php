<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'error' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_SESSION['cliente_id'] ?? null;
  $pedido = $_POST['pedido'] ?? null;
  $tipo_pagamento = $_POST['tipo_pagamento'] ?? null;
  $tipo_entrega = $_POST['tipo_entrega'] ?? null;

  if ($cliente_id === null) {
    send([
      'status' => 401,
      'error' => 'Não autorizado'
    ]);
  }

  if ($pedido === null || $tipo_pagamento === null || $tipo_entrega === null) {
    send([
      'status' => 400,
      'error' => 'Pedido, tipo de pagamento e tipo de entrega são obrigatórios'
    ]);
  }

  //? Puxa os valores do cliente
  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);
  $db = $res->fetch_assoc();

  $n_pedidos = $db['n_pedidos'];
  $db['vlr_frete'] = $n_pedidos == 0 ? 0 : $db['vlr_frete'];

  $vlr_frete = $tipo_entrega == 'entrega' ? $db['vlr_frete'] : 0;

  $vlr_pacote = $db['vlr_pacote'];

  //? Formata o pedido
  $vlr_total = 0;
  if (is_string($pedido)) {
    $pedido = json_decode($pedido, true);
  }
  $pedido_formatado = [];
  foreach ($pedido as $produto) {
    $pedido_formatado[] = [
      'produto_id' => $produto['id'],
      'quantidade' => $produto['qtd'],
      'valor_venda' => $vlr_pacote
    ];
    $vlr_total += $produto['qtd'] * $vlr_pacote;
  }
  $vlr_total += $vlr_frete;

  if ($tipo_pagamento == 'dinheiro') {
    $forma_pagamento = 2633094;
  }elseif ($tipo_pagamento == 'boleto') {
    $forma_pagamento = 2219792;
  }elseif ($tipo_pagamento == 'pix') {
    $forma_pagamento = 2219799;
  }	
  
  $url = "vendas";
  $method = 'POST';
  $data = [
    'tipo' => 'produto',
    'cliente_id' => $cliente_id,
    'situacao_id' => 3395252,
    'date' => date('Y-m-d'),
    'produtos' => $pedido_formatado,
    'valor_frete' => $vlr_frete,
    'vendedor_id' => 1052314,
    'pagamentos' => [
      'pagamento' => [
        'forma_pagamento_id' => $forma_pagamento,
        'valor' => $vlr_total,
        'parcelas' => 1
      ]
    ]
  ];

  $response = gs_click($url, $method, $data);

  //? Soma no numero de pedidos do cliente
  $n_pedidos++;
  $sql = "UPDATE usuarios SET n_pedidos = $n_pedidos WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  send([
    'status' => 200,
    'data' => $data,
    'response' => $response
  ]);
  
?>