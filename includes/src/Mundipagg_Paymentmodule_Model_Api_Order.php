<?php
/**
 * This class is a Wrapper to the MundiPagg SDK
 *
 * @package Mundipagg/Paymentmodule
 */

require_once Mage::getBaseDir('lib') . '/autoload.php';

use MundiAPILib\Models\CreateCancelChargeRequest;
use MundiAPILib\Models\CreateCaptureChargeRequest;
use MundiAPILib\MundiAPIClient;
use MundiAPILib\Configuration as MundiAPILIBConfiguration;
use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Model_Api_Order
{

    public function __construct()
    {
        MPSetup::bootstrap();
    }

    /**
     * @param Varien_Object $paymentInformation
     * @return mixed|string
     */
    public function createPayment(Varien_Object $paymentInformation)
    {
        $paymentMethod = $paymentInformation->getPaymentInfo();
        $paymentMethod = str_replace("_",'',$paymentMethod);
        $paymentModel = Mage::getModel('paymentmodule/api_' . $paymentMethod);
        $orderRequest = $paymentModel->getCreateOrderRequest($paymentInformation);
        $orderController = $this->getOrderController();

        $helperLog = Mage::helper('paymentmodule/log');
        $helperLog->info("Request");
        $helperLog->info(json_encode($orderRequest,JSON_PRETTY_PRINT));

        try {
            $response = $orderController->createOrder($orderRequest);

            $helperLog->info("Response");
            $helperLog->info(json_encode($response,JSON_PRETTY_PRINT));
            
            return $response;
        } catch (\Exception $e) {
            $helperLog->error("Exception: " . $e->getMessage());
            $helperLog->error(json_encode($e->errors,JSON_PRETTY_PRINT));
            return $e->getMessage();
        }
    }

    public function captureCharge($chargeData) {
        return $this->updateCharge($chargeData, new CreateCaptureChargeRequest());
    }

    public function cancelCharge($chargeData) {
        return $this->updateCharge($chargeData, new CreateCancelChargeRequest());
    }

    protected function updateCharge($chargeData,$chargeRequest)
    {
        $chargeController = $this->getChargeController();
        $method = 'captureCharge';
        if ($chargeRequest instanceof CreateCancelChargeRequest) {
            $method = 'cancelCharge';
        }

        $chargeRequest->amount = $chargeData->amount;

        $helperLog = Mage::helper('paymentmodule/log');
        $helperLog->info("Request MANUAL CHARGE UPDATE: " . $method);
        $helperLog->info(json_encode($chargeData,JSON_PRETTY_PRINT));
        $helperLog->info(json_encode($chargeRequest,JSON_PRETTY_PRINT));

        try {
            $response = $chargeController->$method($chargeData->id,$chargeRequest);

            $helperLog->info("Response MANUAL CHARGE UPDATE: " . $method);
            $helperLog->info(json_encode($response,JSON_PRETTY_PRINT));
            return $response;
        } catch (\Exception $e) {
            $helperLog->error("Exception: " . $e->getMessage());
            $helperLog->error(json_encode($e->errors,JSON_PRETTY_PRINT));
            return $e->getMessage();
        }
    }

    protected function getOrderController()
    {
        return $this->getMundiPaggApiClient()->getOrders();
    }

    protected function getChargeController()
    {
        return $this->getMundiPaggApiClient()->getCharges();
    }

    protected function getMundiPaggApiClient()
    {
        MundiAPILIBConfiguration::$BASEURI = 'https://hubapi.mundipagg.com/core/v1';
        MundiAPILIBConfiguration::$basicAuthPassword = '';

        $moduleConfig = MPSetup::getModuleConfiguration();

        return new MundiAPIClient(
            $moduleConfig->getSecretKey()->getValue(),
            ''
        );
    }
}
