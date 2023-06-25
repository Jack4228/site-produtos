<?php

  include_once("connection.inc.php");

  try {
    $sql = file_get_contents('usuarios.sql');
    $pdo->exec($sql);
    echo 'Usuários criadas com sucesso.';
  } catch (PDOException $e) {
    echo 'Erro ao executar criação de usuários: ' . $e->getMessage();
  }

?>