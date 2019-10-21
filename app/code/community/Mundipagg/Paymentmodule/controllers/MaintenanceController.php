<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Core\Maintenance\Services\InfoBuilderService;
use \Mundipagg\Integrity\IntegrityException;
use \Mundipagg\Integrity\IntegrityController;

use \Mundipagg\Magento\Concrete\MagentoModuleCoreSetup;

class Mundipagg_Paymentmodule_MaintenanceController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        \Mage::helper('paymentmodule/exception')->initExceptionHandler();
    }

    public function versionAction()
    {
        try {
            MagentoModuleCoreSetup::bootstrap();

            $infoBuilderService = new InfoBuilderService();
            $infos = $infoBuilderService->buildInfoFromQueryArray(
                \Mage::app()->getRequest()->getParams()
            );

            $output = json_encode($infos, JSON_PRETTY_PRINT);
            if (is_string($infos)) {
                $output = $infos;
            }

            return $this->getResponse()
                ->setBody($output);

        } catch (IntegrityException $e) {
            $this->getResponse()
                ->setBody($e->getMessage())
                ->setHeader($e->getHeader(), $e->getCode(), true);
            return;
        }
    }

    public function logsAction()
    {
        try {
            $this->getIntegrityController()->renderLogInfo();
        } catch (IntegrityException $e) {
            $this->getResponse()
                ->setBody($e->getMessage())
                ->setHeader($e->getHeader(), $e->getCode(), true);
            return;
        }
    }

    public function downloadLogAction()
    {
        try {
            $this->getIntegrityController()->downloadLogFile();
        } catch (IntegrityException $e) {
            $this->getResponse()
                ->setBody($e->getMessage())
                ->setHeader($e->getHeader(), $e->getCode(), true);
            return;
        }
    }

    public function orderAction()
    {
        try {
            $this->getIntegrityController()->renderOrderInfo();
        } catch (IntegrityException $e) {
            $this->getResponse()
                ->setBody($e->getMessage())
                ->setHeader($e->getHeader(), $e->getCode(), true);
        }
    }

    protected function getIntegrityController()
    {
        return new IntegrityController(
            \Mage::helper('paymentmodule/MagentoSystemInfo'),
            \Mage::helper('paymentmodule/MagentoOrderInfo')
        );
    }
}
