<?php

require_once("lib/jwtutil.inc.php");
require_once("config.php");
require_once("../db/connection.inc.php");

$json = file_get_contents('php://input');
$credenciais = json_decode($json);

$responseBody = '';


try {
    $stmt = $pdo->prepare("SELECT id, nome, email, nascimento, admin FROM tb_usuario WHERE (nome = :cred OR email = :cred) AND senha = :senha");
    $stmt->bindParam(':cred', $credenciais->cred);
    $stmt->bindParam(':senha', $credenciais->senha);
    $stmt->execute();
    
    if ($stmt->rowCount() === 1) {

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $payload = [
            "id" => $user['id'],
            "nome" => $user['nome'],
            "email" => $user['email'],
            "nascimento" => $user['nascimento'],
            "admin" => $user['admin']
        ];
        
        $token = JwtUtil::encode($payload, JWT_SECRET_KEY);
        
        $responseBody = '{ "token": "'.$token.'" }';
    } else {
        http_response_code(401);
        $responseBody = '{ "message": "Credenciais invalidas" }';
    }
} catch (PDOException $e) {
    http_response_code(500);
    $responseBody = '{ "message": "Database error: '.$e->getMessage().'" }';
}

echo $responseBody;

?>