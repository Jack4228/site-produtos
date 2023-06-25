<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('categoria.dao.php');
    require_once('../auth/lib/jwtutil.inc.php');
    require_once("../auth/config.php");

    $categoriaDAO = new CategoriaDAO($pdo);

    $id = $_REQUEST['id'];

    $jwt = @getallheaders()['authorization'] ?? '';

    $responseBody = "";

    try {

        $decodedToken = JwtUtil::decode($jwt, JWT_SECRET_KEY);
        $admin = isset($decodedToken['admin']) && $decodedToken['admin'] === 1;

        if (!$id) {
            http_response_code(400);
            $responseBody = '{ "message": "ID não informado"}';
        } else {

            if ($admin) {

                try {
                    $rowsAffected = $categoriaDAO->delete($id);
                    if ($rowsAffected === 1) {
                        http_response_code(200);
                        $responseBody = '';
                    } else {
                        http_response_code(404);
                        $responseBody = '{ "message": "ID não existe" }';
                    }
                } catch (Exception $e) {
                    http_response_code(400);
                    $responseBody = '{ "message": "Ocorreu um erro ao tentar executar esta ação. Erro: Código: ' .  $e->getCode() . '. Mensagem: ' . $e->getMessage() . '" }';
                }

            } else {
                http_response_code(401);
                $responseBody = '{ "message": "Acesso não autorizado" }';
            }

        }

    } catch (Exception $e) {
        http_response_code(401);
        $responseBody = '{ "message": "Token inválido" }';
    }

    header("Content-type: application/json");
    print_r($responseBody);

?>