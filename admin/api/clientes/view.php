<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $id = $_GET['id'] ?? null;
  $comodato = $_GET['comodato'] ?? false;

  //? Puxa o cliente pelo no banco de dados
  $sql = "SELECT * FROM usuarios";
  if ($comodato == true) {
    $sql .= " WHERE qtd_semanal_comodato > 0";
  }
  $res = $conn->query($sql);

  $clientes_registrados = [];
  $clientes_dados = [];
  while ($db = $res->fetch_assoc()) {
    $clientes_registrados[] = $db['cliente_id'];
    $clientes_dados[$db['cliente_id']] = $db;
  }

  $url = "clientes";
  $method = 'GET';
  $data = [
    'ativo' => 1,
  ];
  if ($id !== null) {
    $data['id'] = $id;
  }

  $response = gs_click($url, $method, $data);

  if ($comodato == true) {

    // apenas os clientes do banco de dados
    $clientes = [];
    foreach ($response as $cliente) {
      $registrado = in_array($cliente['id'], $clientes_registrados);
      $acess = base64_encode($cliente['id']);

      if ($registrado == true) {
        $clientes[] = [
          'id' => $cliente['id'],
          'nome' => $cliente['nome'],
          'registrado' => $registrado,
          'magic' => env('MAGIC') . '/api/login/magic?access=' . $acess,
          'dados_internos' => $clientes_dados[$cliente['id']] ?? null
        ];
      }
    }

  }elseif ($id == null) {
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