<?php
  function getDatabaseConnection() {
    // 1. Criar a ligação
    $db = new PDO('sqlite:database/dados.db');

    // 2. Se falhar o SQL, o site avisa.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Definir o formato padrão dos dados, assim não é preciso escrever PDO::FETCH_ASSOC em todos os fetch()
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $db;
  }
?>