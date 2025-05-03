<?php

  //? Verifica se o cliente tem compras no mês

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_GET['cliente_id'] ?? null;
  $inicio = $_GET['inicio'] ?? null; //? YYYY-MM-DD
  $fim = $_GET['fim'] ?? null; //? YYYY-MM-DD

  if ($cliente_id == null || $inicio == null || $fim == null) {
    send([
      'status' => 400,
      'message' => 'Parâmetros inválidos'
    ]);
  }

  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);
  $cliente = $res->fetch_assoc();
  if (!$cliente) {
    send([
      'status' => 404,
      'message' => 'Cliente não encontrado'
    ]);
  }
  
  $meta_semanal = (int) $cliente['qtd_semanal_comodato'] ?? 0;
  
  //? calcula a meta do periodo
  $meta_diaria = $meta_semanal / 7;
  
  $inicio = new DateTime($inicio);
  $fim = new DateTime($fim);
  $interval = $inicio->diff($fim);
  $dias = $interval->days + 1; //? +1 para incluir o dia de inicio

  $meta_periodo = (int) ($meta_diaria * $dias);

  //? Puxa a quantidade de compras do cliente no mês
  $url = 'vendas';
  $method = 'GET';
  $data = [
    'cliente_id' => $cliente_id,
    'data_inicio' => $inicio->format('Y-m-d'),
    'data_fim' => $fim->format('Y-m-d'),
  ];

  $res = gs_click($url, $method, $data);

  $qtd_compras = 0;
  if ($res !== []) {
    foreach ($res as $compra) {
      $produtos = $compra['produtos'];
      foreach ($produtos as $produto) {
        $produto = $produto['produto'];
        $quantidade = $produto['quantidade'] ?? 0;
        $qtd_compras += (int) $quantidade;
      }
    }
  }

  $bateu_meta = $qtd_compras >= $meta_periodo ? true : false;
  $qtd_faltou = $meta_periodo - $qtd_compras;
  $qtd_faltou = $qtd_faltou < 0 ? 0 : $qtd_faltou; //? Se o cliente passou da meta, não falta nada

  send([
    'status' => 200,
    'dias' => $dias,
    'meta_semanal' => $meta_semanal,
    'bateu_meta' => $bateu_meta,
    'meta_periodo' => $meta_periodo,
    'qtd_compras' => $qtd_compras,
    'qtd_faltou' => $qtd_faltou
  ]);


?>