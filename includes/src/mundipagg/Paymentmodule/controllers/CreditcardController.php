<?php

use MundiAPILib\Models\GetOrderResponse;

class Mundipagg_Paymentmodule_CreditcardController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('paymentmodule/exception')->initExceptionHandler();
    }

    /**
     * Only one credit card brand allowed
     */
    public function getInstallmentsAction()
    {
        $brandName[] = key(Mage::app()->getRequest()->getParams());
        $value = $this->getRequest()->getParam('value');
        $installmentConfig = Mage::helper('paymentmodule/installment');

        $grandTotal = floatval($value);
        if($value === null) {
            $grandTotal = Mage::getModel('checkout/session')
                ->getQuote()->getGrandTotal();
        }

        $installments = array();
        if (!empty($brandName[0])) {
            $installments =
                current(
                    $installmentConfig->getInstallments(
                        $grandTotal,
                        $brandName
                    )
                );
        }

        $this->setResponse($installments);
    }

    public function setResponse($response)
    {
        return $this->getResponse()
            ->clearHeaders()
            ->setHeader('HTTP/1.0', 200 , true)
            ->setHeader('Content-Type', 'text/html')
            ->setBody(json_encode($response));
    }
}
