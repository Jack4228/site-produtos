<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('produto.dao.php');
    require_once('../auth/lib/jwtutil.inc.php');
    require_once("../auth/config.php");

    $produtoDAO = new ProdutoDAO($pdo);

    $jwt = @getallheaders()['authorization'] ?? '';

    $json = file_get_contents('php://input');

    $produto = json_decode($json);

    $responseBody = "";

    try {

        $decodedToken = JwtUtil::decode($jwt, JWT_SECRET_KEY);
        $admin = isset($decodedToken['admin']) && $decodedToken['admin'] === 1;

        if ($admin) {

            try {
                $produto = $produtoDAO->insert($produto);
                $responseBody = json_encode($produto);
            } catch (Exception $e) {
                http_response_code(400);
                $responseBody = '{ "message": "Ocorreu um erro ao tentar executar esta ação. Erro: Código: ' .  $e->getCode() . '. Mensagem: ' . $e->getMessage() . '" }';
            }

        } else {
            http_response_code(401);
            $responseBody = '{ "message": "Acesso não autorizado" }';
        }

    } catch (Exception $e) {
        http_response_code(401);
        $responseBody = '{ "message": "Token inválido" }';
    }

    header("Content-type: application/json");
    print_r($responseBody);

?>