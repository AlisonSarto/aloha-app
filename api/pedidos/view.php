<?php

  //? Não é necessário autenticação
  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $id = $_GET['id'] ?? null;  

  $data_inicio = date('Y-m-d', strtotime('-1 month'));

  $url = 'vendas';
  $method = 'GET';
  $data = [
    'cliente_id' => $_SESSION['cliente_id'],
    'data_inicio' => $data_inicio
  ];
  if ($id !== null) {
    $data['id'] = $id;
  }

  $pedidos = gs_click($url, $method, $data);

  if ($pedidos == []) {
    send([
      'status' => 404,
      'message' => 'Nenhuma venda foi encontrado!'
    ]);
  }

  $data = [];
  if ($id !== null) {
    //* Mostrar detalhes do pedido
    $data = $pedidos;
  }else {
    //* Mostrar lista de pedidos
    foreach ($pedidos as $pedido) {
      if ($pedido['nome_situacao'] == 'Em análise') {
        $cor = 'dark';
      }elseif ($pedido['nome_situacao'] == 'Preparando envio') {
        $cor = 'warning';
      }elseif ($pedido['nome_situacao'] == 'Em rota') {
        $cor = 'info';
      }elseif ($pedido['nome_situacao'] == 'Concluído') {
        $cor = 'success';
      }else {
        $cor = 'danger';
      }

      $data[] = [
        'id' => $pedido['id'],
        'codigo' => $pedido['codigo'],
        'data' => date('d/m/Y', strtotime($pedido['data'])),
        'prazo_entrega' => date('d/m/Y', strtotime($pedido['prazo_entrega'])),
        'valor_total' => str_replace('.', ',', $pedido['valor_total']),
        'nome_situacao' => $pedido['nome_situacao'],
        'cor' => $cor
      ];
    }
  }

  send([
    'status' => 200,
    'pedidos' => $data
  ]);

?>