<?php

  $hash = $_GET['hash'];
  $hash = password_hash($hash, PASSWORD_DEFAULT);
  echo $hash;

?>