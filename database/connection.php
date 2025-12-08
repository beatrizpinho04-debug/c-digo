<?php
  function getDatabaseConnection() {
    $db = new PDO('sqlite:database/dados.db');
    return $db;
  }
?>