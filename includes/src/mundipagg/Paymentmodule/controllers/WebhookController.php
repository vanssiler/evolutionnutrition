<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

class Mundipagg_Paymentmodule_WebhookController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('paymentmodule/exception')->initExceptionHandler();
    }

    public function indexAction()
    {
        if (Mage::app()->getRequest()->isPost()) {
            $body = json_decode(Mage::app()->getRequest()->getRawBody());
            /**
             * @var Mundipagg_Paymentmodule_Helper_Log $logger
             */
            $logger = Mage::helper('paymentmodule/log');

            $webhookInfo = explode('.', $body->type);
            $webhookType  = $webhookInfo[0];
            $webhookAction = $webhookInfo[1];

            $orderCode = $body->data->code;
            $logger->info(
                'Webhook ('.$body->type.') received for order #' .
                $orderCode . ":\n" .
                json_encode($body,JSON_PRETTY_PRINT)
            );

            switch ($webhookType) {
                case 'order':
                    $this->webhookOrderUpdate($body->data, $webhookAction);
                    break;
                case 'charge':
                    $chargeOperations = Mage::helper('paymentmodule/chargeoperations');
                    if ($chargeOperations->isTransactionHandled(
                        $body->data->code,
                        $body->data->last_transaction->id
                    )) {
                        $logger->warning(
                            'Transaction ' .
                            $body->data->last_transaction->id .
                            ' already handled.'
                        );

                        return;
                    }
                    $lastTransaction = $body->data->last_transaction;
                    $chargeOperations->setTransactionAsHandled(
                        $body->data->code,
                        array(
                            'id' => $lastTransaction->id,
                            'timestamp' => strtotime($lastTransaction->updated_at),
                            'amount' => $lastTransaction->amount,
                            'type' => $lastTransaction->operation_type,
                            'chargeAmount' => $body->data->amount,
                            'chargeId' => $body->data->id
                        )
                    );

                    $this->webhookChargeUpdate($body->data, $webhookAction);
                    break;
                default:
                    throw new \Exception('Invalid webhook');
            }
        }elseif (Mage::app()->getRequest()->isGet()) {
            return $this->getResponse()
                ->clearHeaders()
                ->setHeader('HTTP/1.0', 200 , true)
                ->setHeader('Content-Type', 'text/html')
                ->setBody('Webhook URL');
        }
    }

    protected function webhookOrderUpdate($order, $action)
    {
        $orderHelper = Mage::helper('paymentmodule/order');
        $orderHelper->updateStatus($order, $action);
    }

    protected function webhookChargeUpdate($charge, $action)
    {
        $chargeHelper = Mage::helper('paymentmodule/charge');
        $chargeHelper->updateStatus($charge, $action);
    }
}
