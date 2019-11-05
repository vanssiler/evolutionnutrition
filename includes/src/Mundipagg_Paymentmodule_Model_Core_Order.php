<?php

class Mundipagg_Paymentmodule_Model_Core_Order  extends Mundipagg_Paymentmodule_Model_Core_Base
{

    protected $infoInstance;

    //Do nothing
    protected function created($webHook)
    {
    }

    /**
     * Set order status as processing
     * Order invoice is created by charge webhook
     * @param stdClass $webHook
     * @throws Varien_Exception
     */
    protected function paid($webHook)
    {
        $standard = Mage::getModel('paymentmodule/standard');
        $order = $standard->getOrderByIncrementOrderId($webHook->code);

        if ($order->getState() != Mage_Sales_Model_Order::STATE_PROCESSING) {
            $order
                ->setState(
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    true,
                    '',
                    true
                );
            $order->save();
        }
    }

    /**
     * @param stdClass $webHook
     * @throws Varien_Exception
     */
    protected function canceled($webHook)
    {
        $standard = Mage::getModel('paymentmodule/standard');
        $invoiceHelper = Mage::helper('paymentmodule/invoice');

        $order = $standard->getOrderByIncrementOrderId($webHook->code);

        if ($order->canUnhold()) {
            $order->unhold();
        }

        if ($invoiceHelper->cancelInvoices($order)) {
            $this->closeOrder($order);
        }

        $order
            ->setState(
                Mage_Sales_Model_Order::STATE_CANCELED,
                true,
                '',
                true
            );
        $order->save();
    }


    protected function paymentFailed($webHook)
    {
        $this->canceled($webHook);
    }


    /**
     * @param object $order
     */
    protected function closeOrder($order)
    {
        $order->setData('state', Mage_Sales_Model_Order::STATE_CLOSED);
        $order->setStatus(Mage_Sales_Model_Order::STATE_CLOSED);
        $order->sendOrderUpdateEmail();
        $order->save();
    }

    public function processOrderAmountChanges(&$paymentData)
    {
        $this->getPaymentHelper()->validate($paymentData);
        $this->applyInterest($paymentData);

        return $paymentData;
    }

    public function applyInterest(&$paymentData)
    {
        $interest = 0;
        foreach ($paymentData as $method => $data) {
            $interest = $this->getInterests($data, $method);
            $paymentData[$method] = $data;
        }

        if ($interest > 0) {
            $this->applyInterestOnSession($interest);
        }
        return $paymentData;
    }

    protected function getInterests(&$data, $method)
    {
        $totalInterest = 0;

        if ($method == 'creditcard') {
            $data = array_map(function ($item) use (&$totalInterest) {
                $monetary = Mage::helper('paymentmodule/monetary');

                $interestHelper = $this->getInterestHelper();
                $enabledBrands = $this->getConfigCards()->getEnabledBrands();

                $interest = $interestHelper->getInterestValue(
                    $item['creditCardInstallments'],
                    $item['value'],
                    $enabledBrands,
                    $item['brand']
                );

                $value = $monetary->toFloat($item['value']) + $interest;
                $item['value'] = number_format($value, 2, '.', '');
                $item['interest'] = $interest;
                $totalInterest += $interest;

                return $item;
            }, $data);
        }

        return $totalInterest;
    }

    protected function applyInterestOnSession($interest)
    {
        $addresses = $this->getInfoInstance()->getQuote()->getAllAddresses();

        foreach ($addresses as $address) {
            $grandTotal = $address->getGrandTotal();
            if ($grandTotal) {
                $address->setMundipaggInterest($interest);
                $address->setGrandTotal($grandTotal + $interest);
            }
        }
    }

    protected function getConfigCards()
    {
        return Mage::getModel('paymentmodule/config_card');
    }

    protected function getInterestHelper()
    {
        return Mage::helper('paymentmodule/interest');
    }

    protected function getPaymentHelper()
    {
        return Mage::helper('paymentmodule/paymentformat');
    }

    public function setInfoInstance($info)
    {
        $this->infoInstance = $info;
        return $this;
    }

    protected function getInfoInstance()
    {
        return $this->infoInstance;
    }

}
