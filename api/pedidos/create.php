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
  $data_entrega = $_POST['data_entrega'] ?? null;
  $obs = $_POST['obs'] ?? '';
  
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

  if ($pedido === null || $tipo_pagamento === null || $tipo_entrega === null || $data_entrega === null) {
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

  $prazo_boleto = $db['prazo_boleto'];

  $observacao = "\n";
  $observacao .= $tipo_entrega == 'entrega' ? '' : "RETIRADA\n";
  $observacao .= $obs;

  $qtd_total = 0;
  foreach ($pedido as $produto) {
    $qtd_total += $produto['qtd'];
  }

  $vlr_pacote = $db['vlr_pacote'];

  //! Tabela de preços
  if ($vlr_pacote	!= 0) {
    $vlr_pacote = $vlr_pacote;

  }elseif ($qtd_total <= 30) {
    $vlr_pacote = 28.00;

  }elseif ($qtd_total <= 100) {
    $vlr_pacote = 25.20;

  }elseif ($qtd_total > 100) {
    $vlr_pacote = 22.40;
  }

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

  $url = "vendas";
  $method = 'POST';
  $data = [
    'tipo' => 'produto',
    'cliente_id' => $cliente_id,
    'situacao_id' => 3395252,
    'date' => date('Y-m-d'),
    'prazo_entrega' => $data_entrega,
    'produtos' => $pedido_formatado,
    'valor_frete' => $vlr_frete,
    'vendedor_id' => 1052314,
    'observacoes' => $observacao,
    'pagamentos' => [
      'pagamento' => [
        'forma_pagamento_id' => $forma_pagamento,
        'valor' => $vlr_total,
        'parcelas' => 1
      ]
    ]
  ];

  if ($tipo_pagamento !== 'boleto') {
    $data['pagamentos']['pagamento']['data_vencimento'] = $data_entrega;
  }else {
    $data['pagamentos']['pagamento']['data_vencimento'] = date('Y-m-d', strtotime($data_entrega . ' + ' . $prazo_boleto . ' days'));
  } 

  $response = gs_click($url, $method, $data);

  $id_venda = $response['id'];
  $codigo_venda = $response['codigo'];

  //? Soma no numero de pedidos do cliente
  $n_pedidos++;
  $sql = "UPDATE usuarios SET n_pedidos = $n_pedidos WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  //* Webhook WhatsApp
  //? Puxa os dados do cliente no Gestão Click
  $url = "clientes/$cliente_id";
  $method = 'GET';
  $response = gs_click($url, $method);

  $nome_cliente = $response['nome'];
  $telefone_cliente = $response['celular'];

  //* remove os +
  $telefone_cliente = str_replace('+', '', $telefone_cliente);
  $telefone_cliente = str_replace('-', '', $telefone_cliente);
  $telefone_cliente = str_replace('(', '', $telefone_cliente);
  $telefone_cliente = str_replace(')', '', $telefone_cliente);
  $telefone_cliente = str_replace(' ', '', $telefone_cliente);
  
  //* Se os 2 primeiros digitos não for 55 adiciona
  if (substr($telefone_cliente, 0, 2) != '55') {
    $telefone_cliente = '55' . $telefone_cliente;
  }

  //? Envia para o botconversa
  $url = "https://new-backend.botconversa.com.br/api/v1/webhooks-automation/catch/11565/gr7Oav8yN3pn/";
  $method = 'POST';
  $header = [];
  $data = [
    'telefone' => $telefone_cliente,
    'id_venda' => $id_venda,
    'codigo_venda' => $codigo_venda,
  ];
  $response = curl($url, $header, $method, $data);

  send([
    'status' => 200,
    'data' => $data,
    'response' => $response
  ]);
  
?>