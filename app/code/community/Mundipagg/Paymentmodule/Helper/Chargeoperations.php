<?php

class Mundipagg_Paymentmodule_Helper_Chargeoperations extends Mage_Core_Helper_Abstract
{

    public function isTransactionHandled($orderId, $transactionId)
    {
        $additionalInformation =
            Mage::getModel('paymentmodule/standard')
                ->getAdditionalInformationForOrder($orderId);

        if (!isset($additionalInformation['mundipagg_payment_handled_transactions'])) {
            return false;
        }

        return in_array(
            $transactionId,
            $additionalInformation['mundipagg_payment_handled_transactions']
        );
    }

    public function setTransactionAsHandled($orderId, $transaction)
    {
        $payment =
            Mage::getModel('paymentmodule/standard')
                ->getOrderByIncrementOrderId($orderId)
                ->getPayment();
        $additionalInformation =
            Mage::getModel('paymentmodule/standard')
                ->getAdditionalInformationForOrder($orderId);

        if (!isset($additionalInformation['mundipagg_payment_handled_transactions'])) {
            $additionalInformation['mundipagg_payment_handled_transactions'] = array();
        }

        if (!$this->isTransactionHandled($orderId,$transaction['id'])) {
            $this->addTransactionHistoryToOrder($orderId, $transaction, $additionalInformation);
            array_push(
                $additionalInformation['mundipagg_payment_handled_transactions'],
                $transaction['id']
            );
            $payment->setAdditionalInformation($additionalInformation);
            $payment->save();
        }
    }

    public function addTransactionHistoryToOrder($orderId, $transaction, &$additionalInformation)
    {
        if (!isset($additionalInformation['mundipagg_payment_transaction_history'])) {
            $additionalInformation['mundipagg_payment_transaction_history'] = array();
        }

        if (!$this->isTransactionHandled($orderId, $transaction['id'])) {
            array_push(
                $additionalInformation['mundipagg_payment_transaction_history'],
                $transaction
            );
        }
    }

    /**
     * @param string $methodName
     * @param stdClass $charge
     */
    public function paidMethods($methodName, $charge, $extraComment = '', $manual = false)
    {
        $orderId = $charge->code;
        $chargeId = $charge->id;

        if (!$this->isChargeAlreadyUpdated($chargeId, $orderId, $methodName)) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

            $moneyHelper = Mage::helper('paymentmodule/monetary');
            $invoiceHelper = Mage::helper('paymentmodule/invoice');

            $paidAmount = $this->getChargePaidAmount($charge);
            $formattedPaidAmount = $moneyHelper->toCurrencyFormat($paidAmount);
            if ($manual) {
                $formattedPaidAmount =
                    'Updated manually through the module. Value: ' .
                    $formattedPaidAmount;
            }

            $invoiceHelper->addInvoiceToOrder($order, $paidAmount);
            $this->updateChargeInfo($methodName, $charge, $formattedPaidAmount);
        }
    }

    /**
     * @param string $methodName
     * @param stdClass $charge
     */
    public function canceledMethods($methodName, $charge, $extraComment = '', $manual = false)
    {
        $orderId = $charge->code;
        $order =
            Mage::getModel('sales/order')
                ->loadByIncrementId($orderId);

        $moneyHelper = Mage::helper('paymentmodule/monetary');
        $canceledAmount = $charge->canceled_amount * 0.01;

        if ($canceledAmount) {
            $extraComment .= $moneyHelper->toCurrencyFormat(
                $this->getChargeCanceledAmount($charge)
            );
        }

        if ($manual) {
            $extraComment =
                'MP - Charge canceled: Updated manually through the module. Value: ' .
                $moneyHelper->toCurrencyFormat($canceledAmount);
        }

        if ($order->getTotalPaid() > 0) {
            $order
                ->setTotalRefunded($canceledAmount)
                ->setBaseTotalRefunded($canceledAmount)
                ->save();
        }

        $this->updateChargeInfo($methodName, $charge, $extraComment);
    }

    /**
     * Common operations for all charges
     * @param string $type charge type (paid, created, etc)
     * @param stdClass $charge Full webhook object
     * @param string $comment additional comments
     */
    public function updateChargeInfo($type, $charge, $comment = '')
    {
        $orderId = $charge->code;
        $charges[] = $charge;

        $standard = Mage::getModel('paymentmodule/standard');
        $standard->addChargeInfoToAdditionalInformation($charges, $orderId);

        $comment = $this->joinComments($type, $charge, $comment);
        $this->addOrderHistory($orderId, $comment);
    }

    /**
     * @param stdClass $charge
     * @return int
     */
    protected function getChargePaidAmount($charge)
    {
        if ($charge->lastTransaction == null) {
            $operation = $charge->last_transaction->operation_type;
            $amount = $charge->last_transaction->amount / 100;
        } else {
            $operation = $charge->lastTransaction->operationType;
            $amount = $charge->lastTransaction->amount / 100;
        }

        //To values for synchronous and asynchronous
        if (
            $operation == 'auth_and_capture' ||
            $operation == 'capture'
        ) {
            return $amount;
        }

        return 0;
    }

    /**
     * @param stdClass $charge
     * @return int
     */
    protected function getChargeCanceledAmount($charge)
    {
        if ($charge->lastTransaction == null) {
            $operation = $charge->last_transaction->operation_type;
            $amount = $charge->last_transaction->amount / 100;
        } else {
            $operation = $charge->lastTransaction->operationType;
            $amount = $charge->lastTransaction->amount / 100;
        }
        if ($operation == 'cancel') {
            return $amount;
        }

        return 0;
    }

    /**
     * Join comments to insert into order history
     * @param string $type
     * @param stdClass $charge
     * @param string $extraComment
     * @return string
     */
    public function joinComments($type, $charge, $extraComment)
    {
        $orderEnum = Mage::getModel('paymentmodule/enum_orderhistory');

        $type = 'charge' . ucfirst($type);
        $comment = $orderEnum->{$type}();
        $comment .= $extraComment . '<br>';
        $comment .= 'Charge id: ' . $charge->id . '<br>';
        $comment .= 'Event: ' . $type . '<br>';
        $comment .= "Acquirer messange: " . $charge->lastTransaction->acquirerMessage;

        return $comment;
    }

    /**
     * Add comments to order history
     * @param int $orderId
     * @param string $comment
     */
    public function addOrderHistory($orderId, $comment)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $order->addStatusHistoryComment($comment, false);
        $order->save();
    }

    public function isChargeAlreadyUpdated($chargeId, $orderId, $chargeType)
    {
        $standard = Mage::getModel('paymentmodule/standard');

        $additionalInfo =
            $standard->getAdditionalInformationForOrder($orderId);

        if (!empty($additionalInfo['mundipagg_payment_module_charges'][$chargeId])) {
            $status =
                $additionalInfo['mundipagg_payment_module_charges'][$chargeId]['status'];

            if (
                $status === $chargeType ||
                $chargeType === 'created' && $status != 'created'
            ) {
                return true;
            }
        }

        return false;
    }
}
