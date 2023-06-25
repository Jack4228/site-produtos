<?php

class ProdutoDAO {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get_id($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_produto WHERE id = ?");
        $stmt->bindParam(1, $_REQUEST['id']);

        $stmt->execute();
        return $stmt->fetchObject();
    }

    public function get_categoria($categoria) {
        $stmt = $this->pdo->prepare("SELECT tb_produto.*
        FROM tb_produto
        JOIN tb_produto_categoria ON tb_produto.id = tb_produto_categoria.id_produto
        JOIN tb_categoria ON tb_produto_categoria.id_categoria = tb_categoria.id
        WHERE tb_categoria.nome = ?");
        $stmt->bindParam(1, $_REQUEST['categoria']);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_produto");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    public function insert($produto) {
        $stmt = $this->pdo->prepare("INSERT INTO tb_produto 
            (nome, descricao, preco, quantidade) 
            VALUES (:nome, :desc, :preco, :quant)");
        
        $stmt->bindValue("nome", $produto->nome);
        $stmt->bindValue("desc", $produto->descricao);
        $stmt->bindValue("preco", $produto->preco);
        $stmt->bindValue("quant", $produto->quantidade);

        $stmt->execute();
        $id_produto = $this->pdo->lastInsertId();

        foreach ($produto->categorias as $categoria) {
            $stmt = $this->pdo->prepare("SELECT id FROM tb_categoria WHERE nome = ?");
            $stmt->bindParam(1, $categoria);
            $stmt->execute();
            $obj = $stmt->fetchObject();
            if ($obj) {
                $id_categoria = $obj->id;

                $stmt = $this->pdo->prepare("INSERT INTO tb_produto_categoria
                (id_produto, id_categoria) 
                VALUES (:ip, :ic)");
            
                $stmt->bindValue("ip", $id_produto);
                $stmt->bindValue("ic", $id_categoria);
                $stmt->execute();
            }
        }
        
        $produto = clone $produto;
        $produto->id = $id_produto;
        return $produto;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tb_produto_categoria WHERE id_produto = :id");
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $stmt = $this->pdo->prepare("DELETE FROM tb_compra_produto WHERE id_produto = :id");
        $stmt->bindValue("id", $id);
        $stmt->execute();

        $stmt = $this->pdo->prepare("DELETE FROM tb_produto 
        WHERE id=:id");
        $stmt->bindValue("id", $id);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function update($id, $produto) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_produto WHERE id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        if (!$stmt->fetchColumn()) {
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM tb_produto_categoria WHERE id_produto = :id");
        $stmt->bindValue("id", $id);
        $stmt->execute();
        
        foreach ($produto->categorias as $categoria) {
            $stmt = $this->pdo->prepare("SELECT id FROM tb_categoria WHERE nome = ?");
            $stmt->bindParam(1, $categoria);
            $stmt->execute();
            $obj = $stmt->fetchObject();
            if ($obj) {
                $id_categoria = $obj->id;

                $stmt = $this->pdo->prepare("INSERT INTO tb_produto_categoria
                (id_produto, id_categoria) 
                VALUES (:ip, :ic)");
            
                $stmt->bindValue("ip", $id);
                $stmt->bindValue("ic", $id_categoria);
                $stmt->execute();
            }
        }
        
        $stmt = $this->pdo->prepare("UPDATE tb_produto SET
                nome = :nome, descricao = :desc,
                preco = :preco, quantidade = :quant
            WHERE id = :id");
        
        $data = [
            "id" => $id,
            "nome" => $produto->nome,
            "desc" => $produto->descricao,
            "preco" => $produto->preco,
            "quant" => $produto->quantidade
        ];
        
        return $stmt->execute($data);
    }
}

?>