CREATE TABLE usuarios (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cliente_id` INT NOT NULL,
  `vlr_pacote` decimal(10,2) NOT NULL DEFAULT '28.00',
  `vlr_frete` decimal(10,2) NOT NULL DEFAULT '0.00',
  `n_pedidos` INT NOT NULL DEFAULT '0',
  `qtd_semanal_comodato` INT NOT NULL DEFAULT '0',
  `prazo_boleto` INT NOT NULL DEFAULT '0',
  `boleto_bloqueado` VARCHAR(45) NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
);

-- Tabela para o CRM
CREATE TABLE leads (
  `id` int NOT NULL AUTO_INCREMENT,
  `telefone` varchar(45) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `status` varchar(45) NOT NULL,
  `primeiro_contato` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE registros (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `respondeu` varchar(45) NOT NULL,
  `comprou` varchar(45) NOT NULL,
  `perdemos` varchar(45) NOT NULL,
  `observacao` varchar(225) NOT NULL,
  `data` varchar(45) NOT NULL,
  `atendente` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
);
