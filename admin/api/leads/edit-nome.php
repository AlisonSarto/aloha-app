<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send(['status' => 405, 'message' => 'Método não permitido']);
  }

  $id = $_POST['id'] ?? null;
  $nome = $_POST['nome'] ?? null;

  if (!$id || !$nome) {
    send(['status' => 400, 'message' => 'ID e nome são obrigatórios']);
  }

  //? Atualiza no banco de dados
  $sql = "UPDATE leads SET nome = '$nome' WHERE id = $id";
  $res = $conn->query($sql);

  if (!$res) {
    send(['status' => 500, 'message' => 'Erro ao atualizar o nome do lead']);
  }

  send(['status' => 200, 'message' => 'Nome do lead atualizado com sucesso']);

?>