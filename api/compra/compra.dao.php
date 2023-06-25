<?php

class CompraDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get_id($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_compra WHERE id = ?");
        $stmt->bindParam(1, $_REQUEST['id']);

        $stmt->execute();
        $compra = $stmt->fetchObject();

        if ($compra) {
            $stmt = $this->pdo->prepare("SELECT tb_compra_produto.quantidade
            FROM tb_compra_produto
            JOIN tb_compra ON tb_compra_produto.id_compra = tb_compra.id
            WHERE tb_compra.id = ?");
            $stmt->bindParam(1, $id);

            $stmt->execute();
            $quantidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $stmt = $this->pdo->prepare("SELECT tb_produto.nome
            FROM tb_produto
            JOIN tb_compra_produto ON tb_produto.id = tb_compra_produto.id_produto
            JOIN tb_compra ON tb_compra_produto.id_compra = tb_compra.id
            WHERE tb_compra.id = ?");
            $stmt->bindParam(1, $id);

            $stmt->execute();
            $nomes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $produtos = [];
            foreach ($nomes as $index => $nome) {
                $produtos[] = [
                    'nome' => $nome,
                    'quantidade' => $quantidades[$index]
                ];
            }

            $compra->produtos = $produtos;
        }
        
        return $compra;
    }

    public function get_usuario($usuario) {
        $stmt = $this->pdo->prepare("SELECT id FROM tb_usuario WHERE email = ?");
        $stmt->bindParam(1, $_REQUEST['usuario']);

        $stmt->execute();
        $id = $stmt->fetchObject();

        if (!$id) {
            return;
        }

        $stmt = $this->pdo->prepare("SELECT id, valor, data FROM tb_compra WHERE id_usuario = ?");
        $stmt->bindParam(1, $id->id);

        $stmt->execute();
        $compras = $stmt->fetchAll(PDO::FETCH_CLASS);

        foreach ($compras as $compra) {
            $stmt = $this->pdo->prepare("SELECT tb_compra_produto.quantidade
            FROM tb_compra_produto
            JOIN tb_compra ON tb_compra_produto.id_compra = tb_compra.id
            WHERE tb_compra.id = ?");
            $stmt->bindParam(1, $compra->id);

            $stmt->execute();
            $quantidades = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $stmt = $this->pdo->prepare("SELECT tb_produto.nome
            FROM tb_produto
            JOIN tb_compra_produto ON tb_produto.id = tb_compra_produto.id_produto
            JOIN tb_compra ON tb_compra_produto.id_compra = tb_compra.id
            WHERE tb_compra.id = ?");
            $stmt->bindParam(1, $compra->id);

            $stmt->execute();
            $nomes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $produtos = [];
            foreach ($nomes as $index => $nome) {
                $produtos[] = [
                    'nome' => $nome,
                    'quantidade' => $quantidades[$index]
                ];
            }

            $compra->produtos = $produtos;
        }
        
        return $compras;
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tb_compra");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }


    public function insert($compra) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_usuario WHERE id = ?");
        $stmt->bindParam(1, $compra->id_usuario);

        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            return;
        }

        $valor = 0;
        $semEstoque = False;
        foreach ($compra->produtos as $produto) {
            $stmt = $this->pdo->prepare("SELECT preco, quantidade FROM tb_produto WHERE nome = ?");
            $stmt->bindParam(1, $produto[0]);
            $stmt->execute();
            $obj = $stmt->fetchObject();
            
            if ($obj){
                $valor += $obj->preco * $produto[1];
                if ($obj->quantidade < $produto[1]) {
                $semEstoque = True;
                }
            }
        }

        $compra->valor = $valor;

        if ($semEstoque) {
            $compra->id = null;
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO tb_compra 
                (id_usuario, valor, data) 
                VALUES (:idu, :valor, :data)");
            
            $data = new DateTime();
            $dataString = $data->format('Y-m-d');

            $stmt->bindValue("idu", $compra->id_usuario);
            $stmt->bindValue("valor", $valor);
            $stmt->bindValue("data", $dataString);

            $stmt->execute();
            $compra->id = $this->pdo->lastInsertId();

            foreach ($compra->produtos as $produto) {
                $stmt = $this->pdo->prepare("SELECT id FROM tb_produto WHERE nome = ?");
                $stmt->bindParam(1, $produto[0]);
                $stmt->execute();
                $obj = $stmt->fetchObject();
                if ($obj) {
                    $id_produto = $obj->id;
                    $stmt = $this->pdo->prepare("INSERT INTO tb_compra_produto
                    (id_compra, id_produto, quantidade) 
                    VALUES (:ic, :ip, :quant)");

                    $stmt->bindValue("ic", $compra->id);
                    $stmt->bindValue("ip", $id_produto);
                    $stmt->bindValue("quant", $produto[1]);
                    $stmt->execute();

                    $stmt = $this->pdo->prepare("UPDATE tb_produto SET quantidade = quantidade - :quant WHERE id = :id");
                    $stmt->bindValue("quant", $produto[1]);
                    $stmt->bindValue("id", $id_produto);
                    $stmt->execute();
                }

                
            }
            
            $compra->valor = $valor;
        }

        $compra = clone $compra;
        return $compra;
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tb_compra_produto 
        WHERE id_compra = :id");
        $stmt->bindValue("id", $id);

        $stmt->execute();

        $stmt = $this->pdo->prepare("DELETE FROM tb_compra 
        WHERE id = :id");
        $stmt->bindValue("id", $id);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function update($id, $compra) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_compra WHERE id = ?");
        $stmt->bindParam(1, $id);

        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            return;
        }

        $stmt = $this->pdo->prepare("SELECT tb_compra_produto.id_produto, tb_compra_produto.quantidade
        FROM tb_compra_produto
        JOIN tb_compra ON tb_compra_produto.id_compra = tb_compra.id
        WHERE tb_compra.id = ?");
        $stmt->bindParam(1, $id);

        $stmt->execute();
        $anterior = $stmt->fetchAll(PDO::FETCH_OBJ);

        $semEstoque = False;
        foreach ($compra->produtos as $produto) {
            $stmt = $this->pdo->prepare("SELECT id, quantidade FROM tb_produto WHERE nome = ?");
            $stmt->bindParam(1, $produto[0]);
            $stmt->execute();
            $obj = $stmt->fetchObject();
            if ($obj) {
                $id_produto = $obj->id;
                $estoque = $obj->quantidade;
                $requisicao = $produto[1];

                $quantAnterior = 0;
                foreach ($anterior as $p) {
                    if ($p->id_produto == $id_produto) {
                        $quantAnterior = $p->quantidade;
                        break;
                    }
                }

                if ($quantAnterior + $estoque < $requisicao) {
                    $semEstoque = True;
                }
            }
        }

        if ($semEstoque) {
            $compra->id = null;
        } else {
            
            foreach($anterior as $produto) {
                $stmt = $this->pdo->prepare("UPDATE tb_produto
                SET quantidade = quantidade + :quant
                WHERE id = :id");
                $stmt->bindValue("quant", $produto->quantidade);
                $stmt->bindValue("id", $produto->id_produto);
                $stmt->execute();
            }

            $stmt = $this->pdo->prepare("DELETE FROM tb_compra_produto WHERE id_compra = :id");
            $stmt->bindValue("id", $id);
            $stmt->execute();

            $valor = 0;
            foreach ($compra->produtos as $produto) {
                $stmt = $this->pdo->prepare("SELECT id, preco FROM tb_produto WHERE nome = ?");
                $stmt->bindParam(1, $produto[0]);
                $stmt->execute();
                $obj = $stmt->fetchObject();
                $id_produto = $obj->id;
                $valor += $obj->preco * $produto[1];

                $stmt = $this->pdo->prepare("INSERT INTO tb_compra_produto
                (id_compra, id_produto, quantidade) 
                VALUES (:ic, :ip, :quant)");
            
                $stmt->bindValue("ic", $id);
                $stmt->bindValue("ip", $id_produto);
                $stmt->bindValue("quant", $produto[1]);
                $stmt->execute();

                $stmt = $this->pdo->prepare("UPDATE tb_produto SET quantidade = quantidade - :quant WHERE id = :id");
                $stmt->bindValue("quant", $produto[1]);
                $stmt->bindValue("id", $id_produto);
                $stmt->execute();
            }

            $stmt = $this->pdo->prepare("SELECT id_usuario FROM tb_compra WHERE id = ?");
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $id_usuario = $stmt->fetchObject()->id_usuario;
            
            $stmt = $this->pdo->prepare("UPDATE tb_compra SET
                    id_usuario = :idu, valor = :valor, data = :data
                WHERE id = :id");

            $data = new DateTime();
            $dataString = $data->format('Y-m-d');
            
            $data = [
                "id" => $id,
                "idu" => $id_usuario,
                "valor" => $valor,
                "data" => $dataString
            ];

            $compra =  $stmt->execute($data);
        }
        
        return $compra;
    }
}

?>