<?php

    include_once("connection.inc.php");

  try {
    $sql = file_get_contents('elementos.sql');
    $pdo->exec($sql);
    echo 'Categorias e produtos criadas com sucesso.';
  } catch (PDOException $e) {
    echo 'Erro ao executar criação de categorias e produtos: ' . $e->getMessage();
  }



?>