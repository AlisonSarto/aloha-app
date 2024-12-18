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
  
  $black_friday = false;
  if (date('Y-m-d') == '2024-11-22' || date('Y-m-d') == '2024-11-23') {
    $black_friday = true;
  }

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
    $tbm_pedido = [
      'produto_id' => $produto['id'],
      'quantidade' => $produto['qtd'],
      'valor_venda' => $vlr_pacote
    ];

    $tbm_valor = $produto['qtd'] * $vlr_pacote;
    
    //* Black Friday 2024 (10% off nos sabores novos)
    if ($black_friday == true) {
      if ($produto['id'] == 70214810 || $produto['id'] == 70214803 || $produto['id'] == 70214781) {
        $tbm_pedido['tipo_desconto'] = '%';
        $tbm_pedido['desconto_porcentagem'] = '10';
        $tbm_pedido['detalhes'] = '10% OFF na Black Friday dos sabores novos';
        $tbm_valor = $tbm_valor - ($tbm_valor * 0.1);
      }
    }

    $pedido_formatado[] = $tbm_pedido;
    $vlr_total += $tbm_valor;
  }
  $vlr_total += $vlr_frete;

  if ($tipo_pagamento == 'dinheiro') {
    $forma_pagamento = 2633094;
  }elseif ($tipo_pagamento == 'boleto') {
    $forma_pagamento = 2219792;
  }elseif ($tipo_pagamento == 'pix') {
    $forma_pagamento = 2219799;
  }

  $prazo_entrega = prazoEntrega(date('Y-m-d'), 2);
  $prazo_entrega = $prazo_entrega->format('Y-m-d');
  
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
    'prazo_entrega' => $prazo_entrega,
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