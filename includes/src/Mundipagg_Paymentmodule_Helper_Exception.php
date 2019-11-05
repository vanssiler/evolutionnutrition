<?php

class Mundipagg_Paymentmodule_Helper_Exception extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mundipagg_Paymentmodule_Helper_Log $logger
     */
    protected $logger;
    protected $isInit;

    public function __construct()
    {
        $this->logger = Mage::helper('paymentmodule/log');

        $this->isInit = false;
    }

    public function initExceptionHandler()
    {
        if (!$this->isInit) {
            $this->isInit = true;
            set_exception_handler(function(Throwable $throwable) {
                $this->registerException($throwable);

                Mage::app()->getFrontController()
                    ->getResponse()
                    ->clearHeaders()
                    ->setRedirect(Mage::getUrl('checkout/onepage/failure'))
                    ->sendResponse();
            });
        }
    }

    public function registerException(Throwable $throwable) {
        $this->logger->error("----------------------------------");
        $this->logger->error("An exception was throwed!");
        $this->logger->error($throwable->getMessage(),true);

        $details = new stdClass();
        $details->file = $throwable->getFile();
        $details->line = $throwable->getLine();
        $details->code = $throwable->getCode();

        $this->logger->error("Details:\n" . json_encode($details,JSON_PRETTY_PRINT));
        $this->logger->error("Trace:\n" . $throwable->getTraceAsString());
        $this->logger->error("----------------------------------");
    }
}