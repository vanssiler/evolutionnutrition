<?php

class Mundipagg_Paymentmodule_ChargeController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('paymentmodule/exception')->initExceptionHandler();
    }

    /**
     * @param $body
     * @return bool
     * @throws Varien_Exception
     */
    protected function isPasswordValid($body)
    {
        $adminUser = Mage::getModel('admin/user')->loadByUsername($body->username);
        list($adminPassword,$adminSalt) = explode(':',$adminUser->getPassword());
        $inputPassword = md5($adminSalt . $body->credential);

        return $inputPassword === $adminPassword;
    }

    protected function setResponse($status, $message, $details = null)
    {
        $response = new stdClass();
        $response->message = $message;
        $response->status = intval($status);
        if ($details) {
            $response->details = $details;
        }

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('HTTP/1.0', 200 , true)
            ->setHeader('Content-Type', 'application/json') // can be changed to json, xml...
            ->setBody(json_encode($response));
    }

    protected function getOrderAditionalInformation($body)
    {
        $orderId = $body->orderId;

        $collection = Mage::getResourceModel('sales/order_collection')
            ->join(array('b' => 'sales/order_payment'),
                'main_table.entity_id = b.parent_id',
                array('additional_information' => 'additional_information')
            )
            ->addFieldToFilter('main_table.entity_id', $orderId);

        $additional = array();
        foreach ($collection as $order) {
            $additional = unserialize($order->additional_information);
        }
        return $additional;
    }

    protected function APICall($charge)
    {
        $api = Mage::getModel('paymentmodule/api_order');
        $apiMethod = 'cancelCharge';
        if ($charge->operation === "Capture") {
            $apiMethod = 'captureCharge';
        }
        return $api->$apiMethod($charge);
    }

    protected function handleAPIResponse($response)
    {
        if(is_string($response)) {
            return false;
        }

        $chargeOperations = Mage::helper('paymentmodule/chargeoperations');
        if ($response->lastTransaction->operationType == "capture") {
            $method = 'paidMethods';
        }
        if ($response->lastTransaction->operationType == "cancel") {
            $method = 'canceledMethods';
        }

        $chargeOperations->$method(
            $response->lastTransaction->operationType,
            $response,
            '',
            true
        );

        return true;
    }

    public function indexAction()
    {
        try {
            if (Mage::app()->getRequest()->isPost()) {
                $body = json_decode(Mage::app()->getRequest()->getRawBody());

                if(!$this->isPasswordValid($body)) {
                    $this->setResponse('403','Invalid password');
                    return;
                }

                //check if the order is valid
                $additional = $this->getOrderAditionalInformation($body);
                if (!isset($additional['mundipagg_payment_module_charges'])) {
                    $this->setResponse('404','Invalid Order');
                    return;
                }

                //check if the order contains the charge
                $charges = $additional['mundipagg_payment_module_charges'];
                if (!isset($charges[$body->id])) {
                    $this->setResponse('404','Invalid Charge');
                    return;
                }

                //defining the amount
                $charge = $body;
                $charge->amount = $charge->centsValue;
                if ($charge->operationType != "total") {
                    $charge->amount = $charge->operationValue;
                }

                $response = $this->APICall($charge);

                if(!$this->handleAPIResponse($response)) {
                    $this->setResponse('403','Operation failed', $response);
                    return;
                }

                if (!$response->lastTransaction->success) {
                    $this->setResponse('403','Operation failed');
                    return;
                }

                $chargeOperations = Mage::helper('paymentmodule/chargeoperations');
                $chargeOperations->setTransactionAsHandled(
                    $response->code,
                    array(
                        'id' => $response->lastTransaction->id,
                        'timestamp' => $response->lastTransaction->updatedAt->getTimestamp(),
                        'amount' => $response->lastTransaction->amount,
                        'type' => $response->lastTransaction->operationType,
                        'chargeAmount' => $response->amount,
                        'chargeId' => $response->id
                    )
                );

                $this->setResponse('200','Success');
                return;
            }
        } catch(Throwable $e) {
            Mage::helper('paymentmodule/exception')->registerException($e);
            $this->setResponse('500','Internal Server error. Please contact support.');
        }
    }
}
