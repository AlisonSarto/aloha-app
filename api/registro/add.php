<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_SESSION['cliente_id'];

  //? Verifica se não existe um usuário com o mesmo id
  $sql = "SELECT * FROM usuarios WHERE cliente_id = '$cliente_id'";
  $res = $conn->query($sql);

  if ($res->num_rows > 0) {
    send([
      'status' => 400,
      'message' => 'Já existe um usuário com esse id'
    ]);
  }

  //? Cria o acesso
  $sql = "INSERT INTO usuarios (cliente_id) VALUES ($cliente_id)";
  $res = $conn->query($sql);

  send([
    'status' => 200,
    'message' => 'Usuário criado com sucesso'
  ]);

?>