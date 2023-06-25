<?php

    include("../enable-cors.php");
    require_once('../db/connection.inc.php');
    require_once('compra.dao.php');
    require_once('../auth/lib/jwtutil.inc.php');
    require_once("../auth/config.php");

    $compraDAO = new CompraDAO($pdo);

    $jwt = @getallheaders()['authorization'] ?? '';

    $responseBody;

    try {

        $decodedToken = JwtUtil::decode($jwt, JWT_SECRET_KEY);
        $admin = isset($decodedToken['admin']) && $decodedToken['admin'] === 1;
        
        if ($admin ?? ($id === $usuario_id)) {
            
            if (@$_REQUEST['usuario']) {
                
                $res = $compraDAO->get_usuario($_REQUEST['usuario']);
                $responseBody = json_encode($res);
                if (!$responseBody) {
                    http_response_code(404);
                    $responseBody = '{ "message": "Usuario não existe" }';
                }
    
            } elseif (@$_REQUEST['id']) {
    
                if ($res = $compraDAO->get_id($_REQUEST['id']))
                    $responseBody = json_encode($res);
                else {
                    http_response_code(404);
                    $responseBody = '{ "message": "Compra não existe" }';
                }
    
            } else {
                $responseBody = json_encode($compraDAO->getAll());
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