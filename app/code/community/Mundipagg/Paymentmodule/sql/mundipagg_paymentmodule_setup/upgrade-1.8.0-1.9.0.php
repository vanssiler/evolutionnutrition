<?php
$installer = $this;

$installer->startSetup();

$prefix = Mage::getConfig()->getTablePrefix();

$installer->run("
      CREATE TABLE IF NOT EXISTS `". $prefix ."paymentmodule_hub_install_token` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `token` TEXT NOT NULL,
            `used` TINYINT(1) NOT NULL,
            `created_at_timestamp` INT NOT NULL,
            `expire_at_timestamp` INT NOT NULL,
            PRIMARY KEY (`id`)
      )"
);

$installer->run("
          CREATE TABLE IF NOT EXISTS `" . $prefix . "paymentmodule_configuration` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `data` TEXT NOT NULL,
          PRIMARY KEY (`id`)
          )          
        "
);

$installer->endSetup();