<?php

// $installer = $this;

// $installer->startSetup();

// $prefix = Mage::getConfig()->getTablePrefix();

// $installer->run("
// CREATE TABLE IF NOT EXISTS `". $prefix ."paymentmodule_savedcreditcard`
// (
//   id                    INT AUTO_INCREMENT PRIMARY KEY,
//   mundipagg_card_id     VARCHAR(255) NOT NULL,
//   holder_name           VARCHAR(255) NULL,
//   mundipagg_customer_id VARCHAR(255) NOT NULL,
//   customer_id           int(11)      NOT NULL,
//   brand_name            VARCHAR(12)  NOT NULL,
//   first_six_digits      VARCHAR(6)   NULL,
//   last_four_digits      VARCHAR(4)   NOT NULL,
//   expiration_date       DATE         NULL,
//   created_at            TIMESTAMP    DEFAULT now(), 
//   updated_at            TIMESTAMP    DEFAULT now() ON UPDATE now() 
// )
// ;
// ");

// $installer->endSetup();