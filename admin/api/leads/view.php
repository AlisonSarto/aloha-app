<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send(['status' => 405, 'message' => 'Metodo não permitido']);
  }

  $sql = "SELECT * FROM registros";
  $res = $conn->query($sql);

  if (!$res) {
    send(['status' => 500, 'message' => 'Erro ao consultar os registros']);
  }

  $registros = [];
  while ($row = $res->fetch_assoc()) {
    $registros[] = $row;
  }

  $sql = "SELECT * FROM leads ORDER BY FIELD(status, 'novo', 'andamento', 'perdido', 'convertido')";
  $res = $conn->query($sql);

  if (!$res) {
    send(['status' => 500, 'message' => 'Erro ao consultar os leads']);
  }

  $leads = [];
  while ($row = $res->fetch_assoc()) {
    $leads[] = $row;
  }

  send([
    'status' => 200,
    'message' => 'Leads consultados com sucesso',
    'leads' => $leads,
    'registros' => $registros
  ]);

?>