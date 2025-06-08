<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send(['status' => 405, 'message' => 'Método não permitido']);
  }

  $lead_id = $_POST['lead_id'] ?? null;
  $tipo = $_POST['tipo'] ?? null;
  $respondeu = $_POST['respondeu'] ?? null;
  $comprou = $_POST['comprou'] ?? null;
  $perdemos = $_POST['perdemos'] ?? null;
  $observacao = $_POST['observacao'] ?? '';
  $atendente = $_POST['atendente'] ?? null;
  $data = date('Y-m-d H:i:s');

  if (
    $lead_id === null ||
    $tipo === null ||
    $respondeu === null ||
    $comprou === null ||
    $atendente === null ||
    $perdemos === null
  ) {
    send(['status' => 400, 'message' => 'Dados incompletos']);
  }

  $sql = "INSERT INTO registros (lead_id, tipo, respondeu, comprou, perdemos, observacao, data, atendente) VALUES ('$lead_id', '$tipo', '$respondeu', '$comprou', '$perdemos', '$observacao', '$data', '$atendente')";
  $res = $conn->query($sql);

  if (!$res) {
    send(['status' => 500, 'message' => 'Erro ao adicionar registro: ' . $conn->error]);
  }

  //? define o status do cliente
  if ($comprou == 'true') {
    $status = 'convertido';
  } elseif ($perdemos == 'true') {
    $status = 'perdido';
  } else {
    $status = 'andamento';
  }

  $sql = "UPDATE leads SET status = '$status' WHERE id = '$lead_id'";
  $res = $conn->query($sql);

  if (!$res) {
    send(['status' => 500, 'message' => 'Erro ao atualizar status do lead: ' . $conn->error]);
  }

  send(['status' => 200, 'message' => 'Registro adicionado com sucesso']);

?>