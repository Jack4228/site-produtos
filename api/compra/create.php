<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('compra.dao.php');
    require_once('../auth/lib/jwtutil.inc.php');
    require_once("../auth/config.php");

    $compraDAO = new CompraDAO($pdo);

    $jwt = @getallheaders()['authorization'] ?? '';

    $json = file_get_contents('php://input');

    $compra = json_decode($json);

    $responseBody = "";

    try {

        $decodedToken = JwtUtil::decode($jwt, JWT_SECRET_KEY);
        $admin = isset($decodedToken['admin']) && $decodedToken['admin'] === 1;
        $compra->id_usuario = $decodedToken['id'];

        try {
            $compra = $compraDAO->insert($compra);
            
            if ($compra->id) {
                http_response_code(200);
                $responseBody = json_encode($compra);
            } else {
                http_response_code(409);
                $responseBody = '{ "message": "Fora de Estoque" }';
            }
        } catch (Exception $e) {
            http_response_code(400);
            $responseBody = '{ "message": "Ocorreu um erro ao tentar executar esta ação. Erro: Código: ' .  $e->getCode() . '. Mensagem: ' . $e->getMessage() . '" }';
        }

    } catch (Exception $e) {
        http_response_code(401);
        $responseBody = '{ "message": "Token inválido" }';
    }

    header("Content-type: application/json");
    print_r($responseBody);

?>