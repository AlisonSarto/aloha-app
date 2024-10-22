<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_GET['id'] ?? null;

  if (!$cliente_id) {
    send([
      'status' => 400,
      'message' => 'Parâmetros inválidos'
    ]);
  }

  $sql = "SELECT * FROM usuarios WHERE cliente_id = $cliente_id";
  $res = $conn->query($sql);

  if ($res->num_rows === 0) {
    send([
      'status' => 404,
      'message' => 'Cliente não encontrado'
    ]);
  }

  $sql = "DELETE FROM usuarios WHERE cliente_id = $cliente_id";
  $conn->query($sql);

  send([
    'status' => 200,
    'message' => 'Cliente deletado com sucesso'
  ]);

?>