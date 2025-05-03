CREATE TABLE usuarios (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cliente_id` INT NOT NULL,
  `vlr_pacote` decimal(10,2) NOT NULL DEFAULT '28.00',
  `vlr_frete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `n_pedidos` INT NOT NULL DEFAULT '0',
  `qtd_semanal_comodato` INT NOT NULL DEFAULT '0',
  `prazo_boleto` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);