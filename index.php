<?php

  include $_SERVER['DOCUMENT_ROOT'] . '/funcs/config.php';

  if ($_SESSION['cliente_id'] === null) {
    header('Location: /login');
  }else{
    header('Location: /app');
  }

?>