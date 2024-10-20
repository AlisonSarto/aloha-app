<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SESSION == []) {
    send([
      'status' => 403,
      'message' => 'Faça login'
    ]);
  }

?>