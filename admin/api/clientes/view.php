<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $id = $_GET['id'] ?? null;

  //? Puxa o cliente pelo no banco de dados
  $sql = "SELECT * FROM usuarios";
  $res = $conn->query($sql);

  $clientes_registrados = [];
  $clientes_dados = [];
  while ($db = $res->fetch_assoc()) {
    $clientes_registrados[] = $db['cliente_id'];
    $clientes_dados[$db['cliente_id']] = $db;
  }

  $url = "clientes";
  $method = 'GET';
  $data = [];
  if ($id !== null) {
    $data = [
      'id' => $id
    ];
  }

  $response = gs_click($url, $method, $data);

  if ($id == null) {
    $clientes = [];
    foreach ($response as $cliente) {
      $registrado = in_array($cliente['id'], $clientes_registrados);
      $acess = base64_encode($cliente['id']);
  
      $clientes[] = [
        'id' => $cliente['id'],
        'nome' => $cliente['nome'],
        'registrado' => $registrado,
        'magic' => env('MAGIC') . '/api/login/magic?access=' . $acess
      ];
    }
  }else {
    $clientes = [];
    foreach ($response as $cliente) {
      $registrado = in_array($cliente['id'], $clientes_registrados);
      $acess = base64_encode($cliente['id']);
  
      $clientes[] = [
        'id' => $cliente['id'],
        'nome' => $cliente['nome'],
        'registrado' => $registrado,
        'magic' => env('MAGIC') . '/api/login/magic?access=' . $acess,
        'dados_internos' => $clientes_dados[$cliente['id']] ?? null
      ];
    }
  }

  send([
    'status' => 200,
    'clientes' => $clientes
  ]);

?>