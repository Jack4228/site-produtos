<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('categoria.dao.php');

    $categoriaDAO = new CategoriaDAO($pdo);

    $responseBody = '';

    try {
        if (@$_REQUEST['id']) {

            if ($res = $categoriaDAO->get($_REQUEST['id']))
                $responseBody = json_encode($res);
            else {
                http_response_code(404);
                $responseBody = '{ "message": "Categoria não existe" }';
            }
        } else {
            $responseBody = json_encode($categoriaDAO->getAll());
        }
    } catch (Exception $e) {
        http_response_code(400);
        $responseBody = '{ "message": "Ocorreu um erro ao tentar executar esta ação. Erro: Código: ' .  $e->getCode() . '. Mensagem: ' . $e->getMessage() . '" }';
    }

    header('Content-type: application/json');
    print_r($responseBody);

?>