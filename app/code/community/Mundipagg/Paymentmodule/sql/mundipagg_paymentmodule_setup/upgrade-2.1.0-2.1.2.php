<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

$defaultStorePlaceHolder = \Mundipagg\Magento\Concrete\MagentoModuleCoreSetup::DEFAULT_STORE_DB_PLACEHOLDER;
$installer = $this;

$installer->startSetup();
$prefix = Mage::getConfig()->getTablePrefix();

$table = $installer->getConnection()->addColumn(
    $prefix . "paymentmodule_configuration",
    'store_id',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => true,
        'comment' => 'store id',
        'default' => $defaultStorePlaceHolder
    ]
);

$installer->endSetup();