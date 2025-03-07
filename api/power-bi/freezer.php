<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $clientes = [
    [
      'id' => 44510737,
      'nome' => 'DISTRIBUIDOR EM SANTO ANDRE',
      'qtd_semana' => 50
    ],
    [
      'id' => 44420305,
      'nome' => 'DISTRIBUIDORA ENZO',
      'qtd_semana' => 50
    ],
    [
      'id' => 44788826,
      'nome' => 'Adega Anhembi',
      'qtd_semana' => 25
    ],
    [
      'id' => 45134839,
      'nome' => 'BAR E ADEGA DE JESUS LTDA',
      'qtd_semana' => 25
    ],
    [
      'id' => 45630297,
      'nome' => 'ADEGA 244',
      'qtd_semana' => 25
    ],
    [
      'id' => 44714510,
      'nome' => 'COMERCIAL SOUSA',
      'qtd_semana' => 50
    ],
  ];

  foreach ($clientes as $key => $cliente) {
    $url = 'vendas';
    $method = 'GET';
    $data = [
      'cliente_id' => $cliente['id']
    ];

    $vendas = gs_click($url, $method, $data);

    foreach ($vendas as $venda) {
      $produtos = $venda['produtos'];
      $data = $venda['data'];
      $data = date('m-Y', strtotime($data));

      foreach ($produtos as $produto) {
        $qtd = $produto['produto']['quantidade'];

        if (!isset($clientes[$key]['vendas'][$data])) {
          $clientes[$key]['vendas'][$data] = 0;
        }

        $clientes[$key]['vendas'][$data] += $qtd;
      }
    }

  }

  send($clientes);

?>