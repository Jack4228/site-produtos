<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('usuario.dao.php');
    require_once('../auth/lib/jwtutil.inc.php');
    require_once("../auth/config.php");

    $usuarioDAO = new UsuarioDAO($pdo);

    $jwt = @getallheaders()['authorization'] ?? '';

    $responseBody;

    try {

        $decodedToken = JwtUtil::decode($jwt, JWT_SECRET_KEY);
        $admin = isset($decodedToken['admin']) && $decodedToken['admin'] === 1;
        
        if ($admin) {
            
            if (@$_REQUEST['id']) {

                if ($res = $usuarioDAO->get($_REQUEST['id'])) {
                    http_response_code(200);
                    $responseBody = json_encode($res);
                } else {
                    http_response_code(404);
                    $responseBody = '{ "message": "Usuário não existe" }';
                }

            } else {
                http_response_code(200);
                $responseBody = json_encode($usuarioDAO->getAll());
            }
            
        } else {
            http_response_code(401);
            $responseBody = '{ "message": "Acesso não autorizado" }';
        }

    } catch (Exception $e) {
        http_response_code(401);
        $responseBody = '{ "message": "Token inválido" }';
    }

    header('Content-type: application/json');
    print_r($responseBody);

?>