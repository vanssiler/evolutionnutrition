<?php

class Mundipagg_Paymentmodule_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_infoBlockType = 'paymentmodule/info';
    protected $_allowCurrencyCode = array();

    public function __construct()
    {
        $this->_allowCurrencyCode = $this->getAllowedCurrencies();
        parent::_construct();
    }

    private function getAllowedCurrencies()
    {
        $currencyCode = Mage::getModel('core/config_data')
            ->getCollection()
            ->addFieldToFilter('path','currency/options/allow')
            ->getData();
        
        $currenciesArray = explode(',', $currencyCode[0]['value']);

        return $currenciesArray;
    }

    public function isAvailable($quote = null)
    {
        return $this->getConfigModel()->isEnabled() &&
            $this->getGeneralConfig()->isEnabled();
    }

    public function getPaymentTitle()
    {
        return $this->getConfigModel()->getPaymentTitle();
    }

    protected function getConfigModel()
    {
        throw new \Exception(
            "Magento tries to instantiate this class," .
            "so this method can't be abstract. " .
            "It must be implemented in child classes."
        );
    }

    public function assignData($data)
    {
        $paymentMethod = $data->getMethod();

        $helperPayment = $this->getHelperPayment();
        $paymentData = $helperPayment->getFormattedData($data->getData(), $paymentMethod);

        try {
            $info = $this->getInfoInstance();
            $this->processOrderAmount($paymentData, $info);
            $info->setAdditionalInformation(
                'mundipagg_payment_method',
                $paymentMethod
            );

            $info->setAdditionalInformation($paymentMethod, $paymentData);
            $info->save();
        } catch (Mage_Core_Exception $e) {
            // @todo log it and do something
        }
        return $this;
    }

    public function getHelperPayment()
    {
        return Mage::helper('paymentmodule/paymentformat');
    }

    protected function processOrderAmount(&$paymentData, $info)
    {
        $orderAmountModel = Mage::getModel('paymentmodule/core_order');
        $orderAmountModel->setInfoInstance($info);
        $orderAmountModel->processOrderAmountChanges($paymentData);
    }

    /**
     * This method defines the controller that will be called when the 'place order' button
     * is pressed, in this case, Mundipagg_Paymentmodule_StandardController, and the specific
     * method, redirectAction.
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        // @fixme _secure is set to false because we are in dev mode
        return Mage::getUrl(
            'paymentmodule/payment/processpayment',
            array('_secure' => false)
        );
    }

    public function getCheckoutSession()
    {
        return Mage::getModel('checkout/session');
    }

    protected function getCustomerDocument($order)
    {
        $taxVat = $order->getCustomerTaxvat();
        $vatId = "";
        try {
            $vatId = $order->getBillingAddress()->getVatId();
        } catch(Exception $e) {
        }
        return !empty($vatId)? $vatId : $taxVat;
    }

    public function getCustomerSession()
    {
        $orderId = Mage::getModel('checkout/session')->getLastOrderId();
        $order = Mage::getModel("sales/order")->load($orderId);

        $customer = new Varien_Object();

        $document = preg_replace(
            '/[^0-9]/',
            '',
            $this->getCustomerDocument($order)
        );

        $name = $order->getCustomerFirstname();
        $name .= ' ' .  $order->getCustomerMiddlename();
        $name .= ' ' .  $order->getCustomerLastname();
        $customer->setName($name);
        $customer->setEmail($order->getCustomerEmail());
        $customer->setId(null);
        $customer->setDocument($document);
        $customer->setCustomer($customer);

        return $customer;
    }

    public function getRegionModel()
    {
        return Mage::getModel('directory/region');
    }

    /**
     * Increment order ids are those ids in the form '100000104'
     *
     * @param string $orderId
     * @return string
     * @throws Varien_Exception
     */
    public function getOrderByIncrementOrderId($orderId)
    {
        return Mage::getModel('sales/order')->loadByIncrementId($orderId);
    }

    public function getOrderByOrderId($orderId)
    {
        return Mage::getModel('sales/order')->load($orderId);
    }

    /**
     * Retrieves additional information for order represented by real order
     * id passed as argument
     *
     * @param string $orderId
     * @return array
    */
    public function getAdditionalInformationForOrder($orderId)
    {
        $order = $this->getOrderByIncrementOrderId($orderId);

        return $order->getPayment()->getAdditionalInformation();
    }

    /**
     * @param $charges
     * @param $orderId
     * @throws Varien_Exception
     */
    public function addChargeInfoToAdditionalInformation($charges, $orderId)
    {
        $order   = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $payment = $order->getPayment();
        $moduleCharges =
            $payment->getAdditionalInformation('mundipagg_payment_module_charges');

        foreach ($charges as $charge) {
            $newInfo[$charge->id] =
                json_decode(json_encode($charge), true);
        }

        if (!empty($newInfo)) {
            $this->addOrUpdateCharge($payment, $newInfo, $moduleCharges);
        }
    }

    protected function addOrUpdateCharge($payment, $info, $charges)
    {
        if (empty($charges)) {
            $payment->setAdditionalInformation(
                'mundipagg_payment_module_charges',
                $info
            );
            return $payment->save();
        }

        $chargeId = key($info);
        $charges[$chargeId] = $info[$chargeId];

        $payment->setAdditionalInformation(
            'mundipagg_payment_module_charges',
            $charges
        );
        return $payment->save();
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getPaymentFromOrder($orderId)
    {
        $order = $this->getOrderByOrderId($orderId);

        return $order->getPayment();
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getChargeInfoFromAdditionalInformation($orderId)
    {
        $payment = $this->getPaymentFromOrder($orderId);

        return $payment->getAdditionalInformation();
    }

    public function getOrderFromCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function getGeneralConfig()
    {
        return Mage::getModel('paymentmodule/config_general');
    }

    public function getConfigData($field, $storeId = null)
    {
        if ($field == 'sort_order') {
            return $this->getConfigModel()->getSortOrder();
        }

        return parent::getConfigData($field, $storeId);
    }
}
