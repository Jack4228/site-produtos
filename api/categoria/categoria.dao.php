<?php

class CategoriaDAO {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_categoria WHERE id = ?");
        $stmt->bindParam(1, $_REQUEST['id']);

        $stmt->execute();
        return $stmt->fetchObject();
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_categoria");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    public function insert($categoria) {
        $stmt = $this->pdo->prepare("INSERT INTO tb_categoria (nome) VALUES (:nome)");
        $stmt->bindValue("nome", $categoria->nome);

        $stmt->execute();
        $categoria = clone $categoria;
        $categoria->id = $this->pdo->lastInsertId();
        return $categoria;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tb_produto_categoria WHERE id_categoria = :id");
        $stmt->bindValue("id", $id);
        $stmt->execute();
        
        $stmt = $this->pdo->prepare("DELETE FROM tb_categoria 
        WHERE id=:id");
        
        $stmt->bindValue("id", $id);

        $stmt->execute();
        
        return $stmt->rowCount();
    }

    public function update($id, $categoria) {
        $stmt = $this->pdo->prepare("UPDATE tb_categoria SET nome = :nome WHERE id = :id");
        
        $data = [
            "id" => $id,
            "nome" => $categoria->nome
        ];

        return $stmt->execute($data);
    }
}

?>