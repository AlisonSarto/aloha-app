<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  $cliente_id = $_GET['cliente_id'] ?? null;

  if ($cliente_id === null) {
    send([
      'status' => 400,
      'message' => 'Cliente ID não informado'
    ]);
  }

  $cliente_id = $_GET['cliente_id'];
  $cliente_id = base64_encode($cliente_id);

  $magic = "/api/login/magic?access=$cliente_id";

  echo "<a href='$magic'>Clique aqui para fazer login</a>";
  echo "<br>";
  echo "<p>$magic</p>";

?>