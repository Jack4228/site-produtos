<?php

  include_once("connection.inc.php");

  try {
    $sql = file_get_contents('tabelas.sql');
    $pdo->exec($sql);
    echo 'Tabelas criadas com sucesso.';
  } catch (PDOException $e) {
    echo 'Erro ao executar criação de tabelas: ' . $e->getMessage();
  }

?>