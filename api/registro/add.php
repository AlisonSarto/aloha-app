<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/access.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $email = $_POST['email'];
  $senha = $_POST['senha'];
  $cliente_id = $_SESSION['cliente_id'];

  //? Criptografando a senha
  $senha = password_hash($senha, PASSWORD_DEFAULT);

  //? Cria o acesso
  $sql = "INSERT INTO usuarios (email, senha, cliente_id) VALUES ('$email', '$senha', $cliente_id)";
  $res = $conn->query($sql);

  send([
    'status' => 200,
    'message' => 'Usuário criado com sucesso'
  ]);

?>