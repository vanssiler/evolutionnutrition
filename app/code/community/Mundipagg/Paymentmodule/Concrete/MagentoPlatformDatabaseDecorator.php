<?php

namespace Mundipagg\Magento\Concrete;

use Mage;
use Mundipagg\Core\Kernel\Abstractions\AbstractDatabaseDecorator;

final class MagentoPlatformDatabaseDecorator extends AbstractDatabaseDecorator
{
    protected function setTableArray()
    {
        //@todo when adding or changing tables, we must remember to create
        //      the migration files to it, fitting with the data
        //      structure of the core repositories.
        $this->tableArray = [
            AbstractDatabaseDecorator::TABLE_MODULE_CONFIGURATION =>
                $this->tablePrefix . "paymentmodule_configuration",

            AbstractDatabaseDecorator::TABLE_HUB_INSTALL_TOKEN =>
                $this->tablePrefix . "paymentmodule_hub_install_token",

            "TEMPLATE_TABLE" =>  $this->tablePrefix . "paymentmodule_recurrencetemplate",
            "TEMPLATE_REPETITION_TABLE" =>  $this->tablePrefix . "paymentmodule_recurrencetemplaterepetition",
            "RECURRENCY_PRODUCT_TABLE" => $this->tablePrefix . "paymentmodule_recurrenceproduct",
            "RECURRENCY_SUBPRODUCT_TABLE" => $this->tablePrefix . "paymentmodule_recurrencesubproduct",
        ];
    }
    protected function doQuery($query)
    {
        $connection = $this->db->getConnection('core_write');
        $connection->query($query);
        $this->setLastInsertId($connection->lastInsertId());
    }
    protected function formatResults($queryResult)
    {
        $retn = new \StdClass;
        $retn->num_rows = count($queryResult);
        $retn->row = array();
        if (!empty($queryResult)) {
            $retn->row = $queryResult[0];
        }
        $retn->rows = $queryResult;
        return $retn;
    }

    protected function doFetch($query)
    {
        $connection = $this->db->getConnection('core_read');

        return $connection->fetchAll($query);
    }
    public function getLastId()
    {
        return $this->db->lastInsertId;
    }
    protected function setTablePrefix()
    {
        $this->tablePrefix = Mage::getConfig()->getTablePrefix();
    }
    protected function setLastInsertId($lastInsertId)
    {
        $this->db->lastInsertId = $lastInsertId;
    }

}