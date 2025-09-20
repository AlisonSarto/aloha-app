<?php

  // Define o diretório raiz do projeto
  $root_dir = dirname(dirname(__DIR__));
  include $root_dir . '/funcs/access.php';
  // Removido include do env.php pois gs_click.php já tem a função env()

  $cliente_id = $_SESSION['cliente_id'];

  //? Profile gestão click
  $url = "clientes";
  $method = 'GET';
  $data = [
    'id' => $cliente_id
  ];
  $profile_gestao = gs_click($url, $method, $data);
  
  // Verifica se a resposta da API externa é válida
  if (!$profile_gestao || !is_array($profile_gestao) || empty($profile_gestao) || isset($profile_gestao['message'])) {
    $profile_gestao = [];
  }

  //? Se ele está com boleto atrasado
  $url = "recebimentos";
  $method = 'GET';
  $data = [
    'data_inicio' => "2023-01-01",
    'cliente_id' => $cliente_id,
    'liquidado' => 'at',
    'forma_pagamento_id' => 2219792 // Boleto
  ];

  $boletos_atrasados = gs_click($url, $method, $data);
  
  $devendo = true;
  if ($boletos_atrasados == []) {
    $devendo = false;
  }

  //? Profile interno
  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows === 0) {
    $db = [];
  }else {
    $db = $res->fetch_all(MYSQLI_ASSOC);
  }

  //? Configurações de entrega
  $dias_antecedencia = env('DIAS_ANTECEDENCIA_ENTREGA');
  
  // Fallback caso a variável não exista ou seja vazia
  if ($dias_antecedencia === false || $dias_antecedencia === null || $dias_antecedencia === '') {
    $dias_antecedencia = 1;
  }

  send([
    'status' => 200,
    'session' => $_SESSION,
    'profile_interno' => $db,
    'profile' => $profile_gestao,
    'boleto_atrasado' => $devendo,
    'config_entrega' => [
      'dias_antecedencia' => intval($dias_antecedencia)
    ]
  ]);

?>