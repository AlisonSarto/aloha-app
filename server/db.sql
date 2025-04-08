CREATE TABLE usuarios (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cliente_id` INT NOT NULL,
  `vlr_pacote` DECIMAL(10,2) NOT NULL DEFAULT 28,
  `vlr_frete` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `n_pedidos` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);