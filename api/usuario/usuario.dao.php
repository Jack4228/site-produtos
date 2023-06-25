<?php

class UsuarioDAO {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_usuario WHERE id = ?");
        $stmt->bindParam(1, $_REQUEST['id']);

        $stmt->execute();
        return $stmt->fetchObject();
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_usuario");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }


    public function insert($usuario) {
        $stmt = $this->pdo->prepare("INSERT INTO tb_usuario 
            (nome, email, senha, nascimento, admin) 
            VALUES (:nome, :email, :senha, :nasc, 0)");
        
        $stmt->bindValue("nome", $usuario->nome);
        $stmt->bindValue("email", $usuario->email);
        $stmt->bindValue("senha", $usuario->senha);
        $stmt->bindValue("nasc", $usuario->nascimento);

        $stmt->execute();
        $usuario = clone $usuario;
        $usuario->id = $this->pdo->lastInsertId();
        return $usuario;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("SELECT id FROM tb_compra WHERE id_usuario = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $compraIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($compraIds)) {
            $inClause = implode(',', array_fill(0, count($compraIds), '?'));
            $stmt = $this->pdo->prepare("DELETE FROM tb_compra_produto WHERE id_compra IN ($inClause)");
            $stmt->execute($compraIds);
            $stmt = $this->pdo->prepare("DELETE FROM tb_compra WHERE id IN ($inClause)");
            $stmt->execute($compraIds);
        }

        $stmt = $this->pdo->prepare("DELETE FROM tb_usuario 
        WHERE id=:id");
        $stmt->bindValue("id", $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function update($id, $usuario) {
        $stmt = $this->pdo->prepare("UPDATE tb_usuario SET
            nome = :nome, email = :email, nascimento = :nascimento
            WHERE id = :id");
        
        $data = [
            "id" => $id,
            "nome" => $usuario->nome,
            "email" => $usuario->email,
            "nascimento" => $usuario->nascimento
        ];

        return $stmt->execute($data);
    }
}

?>