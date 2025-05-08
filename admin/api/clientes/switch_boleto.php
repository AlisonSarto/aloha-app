<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_POST['id'] ?? null;
  $boleto_bloqueado = $_POST['boleto_bloqueado'] ?? null;

  if ($cliente_id === null || $boleto_bloqueado === null) {
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

  //? Atualiza o cliente
  $sql = "UPDATE usuarios SET boleto_bloqueado = '$boleto_bloqueado' WHERE cliente_id = $cliente_id";
  $conn->query($sql);

  send([
    'status' => 200,
    'message' => 'Cliente atualizado com sucesso'
  ]);

?>