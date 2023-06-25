<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('usuario.dao.php');

    $usuarioDAO = new UsuarioDAO($pdo);

    $json = file_get_contents('php://input');

    $usuario = json_decode($json);

    $responseBody = "";

    try {
        $usuario = $usuarioDAO->insert($usuario);
        http_response_code(200);
        $responseBody = json_encode($usuario);
    } catch (Exception $e) {
        http_response_code(400);
        $responseBody = '{ "message": "Ocorreu um erro ao tentar executar esta ação. Erro: Código: ' .  $e->getCode() . '. Mensagem: ' . $e->getMessage() . '" }';
    }

    header("Content-type: application/json");
    print_r($responseBody);

?>