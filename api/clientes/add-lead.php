<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send(['status' => 405, 'message' => 'Método não permitido']);
  }

  $dados = file_get_contents('php://input');
  $dados = json_decode($dados, true);

  $telefone = $dados['telefone'] ?? '';
  $nome = $dados['nome'] ?? '';

  if (empty($telefone) || empty($nome)) {
    send(['status' => 400, 'message' => 'Telefone e nome são obrigatórios']);
  }

  $now = date('Y-m-d');

  $sql = "INSERT INTO leads (nome, telefone, primeiro_contato, status) VALUES ('$nome', '$telefone', '$now', 'novo')";
  $res = $conn->query($sql);

  if (!$res) {
    send(['status' => 500, 'message' => 'Erro ao adicionar lead: ' . $conn->error]);
  }

  send(['status' => 200, 'message' => 'Lead adicionado com sucesso']);

?>