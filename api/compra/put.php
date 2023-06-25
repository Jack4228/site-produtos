<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('compra.dao.php');
    require_once('../auth/lib/jwtutil.inc.php');
    require_once("../auth/config.php");

    $compraDAO = new CompraDAO($pdo);

    $id = $_REQUEST["id"];

    $jwt = @getallheaders()['authorization'] ?? '';

    $json = file_get_contents('php://input');

    $compra = json_decode($json);

    $responseBody = "";

    try {

        $decodedToken = JwtUtil::decode($jwt, JWT_SECRET_KEY);
        $admin = isset($decodedToken['admin']) && $decodedToken['admin'] === 1;
        $usuario_id = $decodedToken['id'];

        if (!$id) {
            http_response_code(400);
            $responseBody = '{ "message": "ID não informado"}';
        } else {

            if ($admin ?? ($id === $usuario_id)) {
                try {
                    $res = $compraDAO->update($id, $compra);
                    if ($res) {
                        if ($res->id) {
                            $responseBody = json_encode($res);
                        } else {
                            http_response_code(409);
                            $responseBody = '{ "message": "Fora de Estoque" }';
                        }
                    } else {
                        http_response_code(404);
                        $responseBody = '{ "message": "Compra não existe" }';
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