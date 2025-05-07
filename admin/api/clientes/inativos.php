<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php'; 

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $tempo_sem_atividade = $_GET['dias_inativo'] ?? 60; //? Dias sem atividade para considerar o cliente inativo

  $inicio = date('Y-m-d', strtotime("-$tempo_sem_atividade days"));
  $fim = date('Y-m-d');

  //? Puxas todos os clientes
  $url = 'clientes';
  $method = 'GET';
  $data = [];

  $clientes = gs_click($url, $method, $data);

  //? Puxa as vendas de cada cliente
  $inativos = [];
  foreach ($clientes as $cliente) {

    $cliente_id = $cliente['id'];

    $url = 'vendas';
    $method = 'GET';
    $data = [
      'cliente_id' => $cliente_id,
      'data_inicio' => $inicio,
      'data_fim' => $fim
    ];

    $vendas = gs_click($url, $method, $data);

    if (count($vendas) == 0) {
      // puxa a venda mais antiga do cliente e define quantos dias ele está inativo
      $url = 'vendas';
      $method = 'GET';
      $data = [
        'cliente_id' => $cliente_id,
      ];

      $venda_cliente = gs_click($url, $method, $data);
      
      if (count($venda_cliente) == 0) {
        $dias_inativo = 'Nunca comprou';        
      }else {
        $venda_cliente = $venda_cliente[0];
        $data_venda = $venda_cliente['data'];
        $dias_inativo = date_diff(date_create($data_venda), date_create(date('Y-m-d')))->days;
      }

      $inativos[] = [
        'id' => $cliente['id'],
        'nome' => $cliente['nome'],
        'razao_social' => $cliente['razao_social'],
        'celular' => $cliente['celular'],
        'dias_inativo' => $dias_inativo,
      ];
    }

  }

  $qtd_clientes_inativos = count($inativos);
  if ($qtd_clientes_inativos == 0) {
    send([
      'status' => 200,
      'message' => 'Nenhum cliente inativo encontrado',
      'inativos' => $inativos
    ]);
  }

  //? Retorna os clientes inativos
  send([
    'status' => 200,
    'message' => $qtd_clientes_inativos . ' clientes inativos',
    'clientes' => $inativos
  ]);

?>