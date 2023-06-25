<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('produto.dao.php');

    $produtoDAO = new ProdutoDAO($pdo);

    $responseBody = '';

    try {
        if (@$_REQUEST['categoria']) {
            
            if ($res = $produtoDAO->get_categoria($_REQUEST['categoria']))
                $responseBody = json_encode($res);
            else {
                http_response_code(404);
                $responseBody = '{ "message": "Categoria não existe" }';
            }

        } elseif (@$_REQUEST['id']) {

            if ($res = $produtoDAO->get_id($_REQUEST['id']))
                $responseBody = json_encode($res);
            else {
                http_response_code(404);
                $responseBody = '{ "message": "Produto não existe" }';
            }

        } else {
            $responseBody = json_encode($produtoDAO->getAll());
        }
    } catch (Exception $e) {
        http_response_code(400);
        $responseBody = '{ "message": "Ocorreu um erro ao tentar executar esta ação. Erro: Código: ' .  $e->getCode() . '. Mensagem: ' . $e->getMessage() . '" }';
    }

    header('Content-type: application/json');
    print_r($responseBody);

?>