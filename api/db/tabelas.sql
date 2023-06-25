
CREATE TABLE tb_usuario (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    nome varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    senha varchar(20) NOT NULL default '',
    nascimento date NOT NULL,
    admin TINYINT(1) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE(nome),
    UNIQUE(email)
    )
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tb_produto (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    nome varchar(100) NOT NULL,
    descricao varchar(200) NOT NULL,
    preco float(7,2) NOT NULL,
    quantidade int UNSIGNED NOT NULL default 0,
    PRIMARY KEY (id),
    UNIQUE(nome)
    )
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tb_categoria (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    nome varchar(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE(nome)
    )
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tb_produto_categoria (
  id_produto int UNSIGNED NOT NULL,
  id_categoria int UNSIGNED NOT NULL
  )
  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tb_compra (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario int UNSIGNED NOT NULL,
    valor float(8,2) NOT NULL,
    data date NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_usuario) REFERENCES tb_usuario(id)
    )
    ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tb_compra_produto (
  id_compra int UNSIGNED NOT NULL,
  id_produto int UNSIGNED NOT NULL,
  quantidade int UNSIGNED NOT NULL,
  FOREIGN KEY (id_compra) REFERENCES tb_compra(id),
  FOREIGN KEY (id_produto) REFERENCES tb_produto(id)
  )
  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;