<?php

use MundiAPILib\Models\GetOrderResponse;

class Mundipagg_Paymentmodule_Model_Paymentmethods_Standard extends Mundipagg_Paymentmodule_Model_Standard
{
    /**
     * Gather boleto transaction information and try to create
     * payment using sdk api wrapper.
     */
    public function processPayment($method)
    {
        $apiOrder = Mage::getModel('paymentmodule/api_order');

        $paymentInfo = new Varien_Object();

        $paymentInfo->setItemsInfo($this->getItemsInformation());
        $paymentInfo->setCustomerInfo($this->getCustomerInformation());
        $paymentInfo->setPaymentInfo($method);
        $paymentInfo->setShippingInfo($this->getShippingInformation());
        $paymentInfo->setMetaInfo(Mage::helper('paymentmodule/data')->getMetaData());

        $response = $apiOrder->createPayment($paymentInfo);

        return $this->handleOrderResponse($response);
    }

    /**
     * Take the result from processPaymentTransaction, add the histories and, if $redirect is true,
     * redirect customer to success page.
     *
     * @param $response
     * @param bool $redirect
     */
    protected function handleOrderResponse($response)
    {
        $redirectTo = Mage::helper('paymentmodule/redirect');

        if (
            gettype($response) !== 'object' ||
            get_class($response) != GetOrderResponse::class
        ) {
            $this->handleCreateOrderError($response);
            $redirectTo->orderFailure();

            return false;
        }

        if ($response->status === 'failed') {
            $this->handleOrderStatusFailed($response);
            $redirectTo->orderFailure();

            return false;
        }

        $this->handleOrderSuccess($response);
        $redirectTo->orderSuccess();

        return true;
    }

    private function handleCreateOrderError($response)
    {
        $helperLog = Mage::helper('paymentmodule/log');
        $orderId = $this->lastRealOrderId;
        $helperLog->error("Invalid response for order #$orderId: ");
        $helperLog->error(json_encode($response,JSON_PRETTY_PRINT));
    }

    private function handleOrderStatusFailed($response)
    {
        if (!empty($response->code) && is_object($response->charges[0])) {
            $chargeHelper = Mage::helper('paymentmodule/charge');
            $order = $this->getOrderByIncrementOrderId($response->code);

            foreach ($response->charges as $chargeIndex => $charge) {
                $chargeHelper->updateStatus($charge, $charge->status);
            }

            $order->cancel()->save();
        }
    }

    private function handleOrderSuccess($response)
    {
        $chargeHelper = Mage::helper('paymentmodule/charge');
        $orderHelper = Mage::helper('paymentmodule/order');

        $savedCreditCard = Mage::helper('paymentmodule/savedcreditcard');
        $savedCreditCard->saveCards($response);

        //get additional information about boleto payments
        $standard = Mage::getModel('paymentmodule/standard');
        $orderId = $response->code;
        $additionalInformation = $standard->getAdditionalInformationForOrder($orderId);
        $paymentMethod = $additionalInformation['mundipagg_payment_method'];
        $paymentInfo = $additionalInformation[$paymentMethod];
        $boletosInfo = array();

        if (isset($paymentInfo['boleto'])) {
            $boletosInfo = $paymentInfo['boleto'];
        }

        /**
         * @todo fix charge handle
         */
        //processing charges;
        $chargeOperations = Mage::helper('paymentmodule/chargeoperations');
        foreach ($response->charges as $chargeIndex => $charge) {
            $charge->code = $response->code;

            $chargeHelper->updateStatus($charge, $charge->status);

            //search for boleto link
            if ($charge->paymentMethod === 'boleto') {
                $boletoUrl = $charge->lastTransaction->url;
                //add to additional information boleto link.
                foreach($boletosInfo as &$boletoInfo){
                    if(!isset($boletoInfo['url'])) {
                        $boletoInfo['url'] =  $boletoUrl;
                        break;
                    }
                }
            }

            $chargeOperations->setTransactionAsHandled(
                $charge->code,
                array(
                    'id' => $charge->lastTransaction->id,
                    'timestamp' => $charge->lastTransaction->updatedAt->getTimestamp(),
                    'amount' => $charge->lastTransaction->amount,
                    'type' => $charge->lastTransaction->operationType,
                    'chargeAmount' => $charge->amount,
                    'chargeId' => $charge->id,
                )
            );
        }

        if(count($boletosInfo) > 0) {
            $this->updateAdditionalInformationWithBoletoLink(
                $paymentMethod,
                $additionalInformation,
                $boletosInfo,
                $standard,
                $orderId
            );
        }

        $order = $standard->getOrderByIncrementOrderId($orderId);
        $order->sendNewOrderEmail();

        //Update magento order status
        $orderHelper->updateStatus($response, $response->status);
    }

    /**
     * Gather information about customer
     *
     * @return Varien_Object
     * @throws Varien_Exception
     */
    protected function getCustomerInformation()
    {
        $standard = Mage::getModel('paymentmodule/standard');
        $customerSession = $standard->getCustomerSession();

        $customer = $customerSession->getCustomer();
        $customerId = $customer->getId();

        $information = new Varien_Object();

        $information->setName($customer->getName());
        $information->setEmail($customer->getEmail());
        $information->setDocument($customer->getDocument());
        // @todo where does it should come from?
        $information->setType('individual');
        $information->setAddress($this->getCustomerAddressInformation());
        $information->setMetadata(null);
        $information->setPhones($this->getCustomerPhonesInformation());
        $information->setCode($customerId);

        return $information;
    }

    /**
     * Gather information about customer's address
     *
     * @return Varien_Object
     * @throws Varien_Exception
     */
    protected function getCustomerAddressInformation()
    {
        return Mage::helper('paymentmodule/address')->getCustomerAddressInformation();
    }

    protected function getShippingInformation($order = null)
    {
        if (!$order) {
            $standard = Mage::getModel('paymentmodule/standard');
            $checkoutSession = $standard->getCheckoutSession();
            $orderId = $checkoutSession->getLastOrderId();
            $order = $standard->getOrderByOrderId($orderId);
        }

        $monetaryHelper = Mage::helper('paymentmodule/monetary');
        $shipping = new Varien_Object();
        $amount = number_format($order->getShippingAmount(), 2);
        $shipping->setAmount($monetaryHelper->toCents($amount));
        $shipping->setDescription($order->getShippingDescription());
        $shipping->setAddress($this->getShippingAddressInformation($order));
        $shipping->setMethod($order->getShippingMethod());

        return $shipping;
    }

    protected function getShippingAddressInformation($order = null) {
        return Mage::helper('paymentmodule/address')
            ->getShippingAddressInformation($order);
    }

    /**
     * Gather information about customer's phones
     *
     * @return Varien_Object
     */
    protected function getCustomerPhonesInformation()
    {
        //loading order to get addresses and phone.
        $standard = Mage::getModel('paymentmodule/standard');
        $checkoutSession = $standard->getCheckoutSession();
        $orderId = $checkoutSession->getLastRealOrderId();
        $order = $standard->getOrderByIncrementOrderId($orderId);

        //filtering numbers from phone number
        $rawBillingPhone = $order->getBillingAddress()->getTelephone();

        $phoneHelper = Mage::helper('paymentmodule/phone');
        return $phoneHelper->extractPhoneVarienFromRawPhoneNumber($rawBillingPhone);
    }

    /**
     * Provide ordered items information
     * @return array
     */
    protected function getItemsInformation()
    {
        $items = array();

        $standard = Mage::getModel('paymentmodule/standard');
        $checkoutSession = $standard->getCheckoutSession();
        $orderId = $checkoutSession->getLastRealOrderId();

        $order = $standard->getOrderByIncrementOrderId($orderId);

        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId() === null) {
                $itemInfo = array();

                $itemInfo['code'] = substr($item->getId(), 0, 52);
                $itemInfo['amount'] = round($item->getPrice() * 100);
                $itemInfo['quantity'] = (int) $item->getQtyOrdered();
                $itemInfo['description'] = $item->getName();
                $items[] = $itemInfo;
            }
        }

        return $items;
    }

    protected function updateAdditionalInformationWithBoletoLink(
        $paymentMethod,
        $additionalInformation,
        $boletosInfo,
        $standard,
        $orderId
    )
    {
        $additionalInformation[$paymentMethod]['boleto'] = $boletosInfo;
        $payment = $standard->getOrderByIncrementOrderId($orderId)->getPayment();
        $payment->setAdditionalInformation(
            $paymentMethod,
            $additionalInformation[$paymentMethod]
        );
        $payment->save();
    }
}
