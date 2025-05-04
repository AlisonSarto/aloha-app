<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send([
      'status' => 405,
      'message' => 'Método não permitido'
    ]);
  }

  $cliente_id = $_POST['id'] ?? null;
  $vlr_frete = $_POST['vlr_frete'] ?? null;
  $vlr_pacote = $_POST['vlr_pacote'] ?? null;
  $qtd_semanal = $_POST['qtd_semanal'] ?? null;
  $prazo_boleto = $_POST['prazo_boleto'] ?? null;

  if ($cliente_id === null || $vlr_frete === null || $vlr_pacote === null || $qtd_semanal === null || $prazo_boleto === null) {
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
  $sql = "UPDATE usuarios SET vlr_frete = $vlr_frete, vlr_pacote = $vlr_pacote, qtd_semanal_comodato = $qtd_semanal, prazo_boleto = $prazo_boleto WHERE cliente_id = $cliente_id";
  $conn->query($sql);

  send([
    'status' => 200,
    'message' => 'Cliente atualizado com sucesso'
  ]);

?>