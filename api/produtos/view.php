<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $id = $_GET['id'] ?? null;  

  $data_inicio = date('Y-m-d', strtotime('-1 month'));

  $url = 'produtos';
  $method = 'GET';
  $data = [];
  if ($id !== null) {
    $data['id'] = $id;
  }
  // $data['grupo_id'] = 2571246;

  $produtos = gs_click($url, $method, $data);

  if ($produtos == []) {
    send([
      'status' => 404,
      'message' => 'Nenhuma venda foi encontrado!'
    ]);
  }

  $data = [];
  foreach ($produtos as $produto) {
    $data[] = [
      'id' => $produto['id'],
      'nome' => $produto['nome'],
    ];
  }

  send([
    'status' => 200,
    'produtos' => $data
  ]);

?>