<?php

  //! Não me pergunte pq a func env() está aqui,
  //! mas ela é necessária para o funcionamento da função gs_click()
  //! então NÃO APAGUE
  function env($envName) {

    //* Define as variáveis de ambiente por ordem de prioridade
    $root = $_SERVER['DOCUMENT_ROOT'];

    //? Verifica se tem public_html no caminho (apenas em produção)
    if (strpos($root, '/public_html') !== false) {
      $root = substr($root, 0, strpos($root, '/public_html'));
    }

    $root .= '/.env.prod';

    $env = [
      "Produção" => $root,
      "Localhost" => $_SERVER['DOCUMENT_ROOT'].'/.env.local',
    ];
    
    //? Verifica se o arquivo existe
    $envFile = null;
    foreach ($env as $type => $file) {
      if (file_exists($file)) {
        $envFile = $file;
        $envType = $type;
        break;
      }
    }

    if (!$envFile) {
      echo "Arquivo .env não encontrado";
      exit;
    }

    $envContent = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $envData = [];

    foreach ($envContent as $line) {
      if (strpos($line, '#') !== false) {
        continue;
      }
      [$key, $value] = explode('=', $line, 2);
      $envData[$key] = $value;
    }

    if (array_key_exists($envName, $envData)) {
      return $envData[$envName];
    } else {
      echo "Variável de ambiente $envName não encontrada no env $envType";
      exit;
    }
    
  }

  function gs_click($url, $method, $data = []) {

    $gs_access_token = env('GS_ACCESS_TOKEN');
    $gs_secret_token = env('GS_SECRET_TOKEN');

    $url = "https://api.beteltecnologia.com/$url";

    $header[] = 'Content-Type: application/json; charset=UTF-8';
    $header[] = "access-token: $gs_access_token";
    $header[] = "secret-access-token: $gs_secret_token";

    if ($method === 'GET') {

      $url = $url . '?' . http_build_query($data);

    }else {

      $data = json_encode($data);

    }

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => $header,
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        
      return [
        'message' => 'Erro cURL #'.$err
      ];

    }

    curl_close($curl);

    $response = json_decode($response, true);

    $result = $response['data'];

    if ($result == 'Não há dados!') {
      return [];
    }

    //? Caso tenha mais de uma página
    $n_paginas = $response['meta']['total_paginas'] ?? 1;

    $n_pagina = 2;
    $x = 1;

    while ($x < $n_paginas) {
      $data['pagina'] = $n_pagina;

      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $header,
      ]);

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      $response = json_decode($response, true);
      $response = $response['data'];

      $result = array_merge($result, $response);

      $n_pagina++;
      $x++;
    }

    return $result;

  }

?>