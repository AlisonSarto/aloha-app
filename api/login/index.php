<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $sql = "SELECT * FROM usuarios";
  $res = $conn->query($sql);

  if ($res->num_rows === 0) {
    send([
      'status' => 404,
      'message' => 'Usuário não encontrado'
    ]);
  }

  while ($row = $res->fetch_assoc()) {
    if ($row['email'] === $email && password_verify($senha, $row['senha'])) {

      $_SESSION['cliente_id'] = $row['cliente_id'];
      send([
        'status' => 200,
        'message' => 'Logado com sucesso',
        'session' => $_SESSION
      ]);
    }
  }

  send([
    'status' => 401,
    'message' => 'E-mail e/ou senha inválidos'
  ]);

?>