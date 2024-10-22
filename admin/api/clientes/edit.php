<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_POST['id'] ?? null;
  $email = $_POST['email'] ?? null;
  $vlr_frete = $_POST['vlr_frete'] ?? null;
  $vlr_pacote = $_POST['vlr_pacote'] ?? null;

  if (!$cliente_id || !$email || !$vlr_frete || !$vlr_pacote) {
    send([
      'status' => 400,
      'message' => 'Parâmetros inválidos'
    ]);
  }

  //? Verifica se o cliente existe
  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows === 0) {
    send([
      'status' => 404,
      'message' => 'Cliente não encontrado'
    ]);
  }

  //? Verifica se o email já está em uso
  $sql = "SELECT * FROM usuarios WHERE email = '$email' AND cliente_id != $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows > 0) {
    send([
      'status' => 400,
      'message' => 'Email já cadastrado'
    ]);
  }

  //? Atualiza o cliente
  $sql = "UPDATE usuarios SET email = '$email', vlr_frete = $vlr_frete, vlr_pacote = $vlr_pacote WHERE cliente_id = $cliente_id";
  $conn->query($sql);

  send([
    'status' => 200,
    'message' => 'Cliente atualizado com sucesso'
  ]);

?>