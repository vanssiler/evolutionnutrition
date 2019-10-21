<?php

class Mundipagg_Paymentmodule_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paymentmodule/info.phtml');
    }

    /**
     * Retrieve payment method
     */
    public function getPaymentMethod()
    {
        return $this->getInfo()->getAdditionalInformation('mundipagg_payment_method');
    }

    public function methodHandler()
    {
        $paymentMethod = str_replace(
          'paymentmodule_',
          '',
            $this->getPaymentMethod()
        );

        return 'handler' . ucfirst($paymentMethod);
    }

    public function handlerTwocreditcards()
    {
        return $this->handlerCreditcard();
    }

    public function handlerBoletocc()
    {
        return array_merge(
            $this->handlerBoleto(),
            $this->handlerCreditcard()
        );
    }

    public function handlerBoleto()
    {
        $boletoData = [];
        foreach ($this->getCharges() as  $key => $charge) {
            if ($charge['payment_method'] !== 'boleto') {
                continue;
            }
            $boletoData[$key]['Nosso Número'] = $this->getBoletoNumber($charge['last_transaction']);
            $boletoData[$key]['Código de Barras'] = $this->getLine($charge['last_transaction']);
            $boletoData[$key]['Link'] = $this->getLink($charge['last_transaction']);
        }

        return $boletoData;
    }

    public function handlerCreditcard()
    {
        $monetary = Mage::helper('paymentmodule/monetary');
        $creditCardData = [];
        foreach ($this->getCharges() as $key => $charge) {

            if (
                $charge['payment_method'] !== 'credit_card' &&
                $charge['payment_method'] !== 'voucher'
            ) {
                continue;
            }

            $lastTransaction = $charge['last_transaction'];

            $creditCardData[$key]['Credit card brand'] =
                $this->getCreditCardBrand($lastTransaction);
            $creditCardData[$key]['Masked number'] =
                $this->getCreditCardNumber($lastTransaction);
            $creditCardData[$key]['Amount'] =
                $monetary->toCurrencyFormat($monetary->toFloat($charge['amount']));

            if ($charge['payment_method'] !== 'voucher') {
                $creditCardData[$key]['Installments'] = $lastTransaction['installments'];
            }
            $creditCardData[$key]['Authorization code'] =
                $this->getAuthorizationCode($lastTransaction);
            $creditCardData[$key]['Transaction ID'] =
                $this->getTransactionId($lastTransaction);
            $creditCardData[$key]['NSU'] = $this->getNSU($lastTransaction);
            $creditCardData[$key]['Acquirer message'] =
                $this->getAcquireMessage($lastTransaction);
            $creditCardData[$key][''] = "";
        }

        return $creditCardData;
    }

    public function handlerVoucher()
    {
        return $this->handlerCreditcard();
    }

    public function getCharges()
    {
        return $this->getInfo()->getAdditionalInformation('mundipagg_payment_module_charges');
    }

    public function getCreditCardBrand($lastTransaction)
    {
        if (!empty($lastTransaction['card'])) {
            return $lastTransaction['card']['brand'];
        }
        return "";
    }

    public function getCreditCardNumber($lastTransaction)
    {
        if (!empty($lastTransaction['card'])) {
            return $lastTransaction['card']['first_six_digits'] .
            "******" .
            $lastTransaction['card']['last_four_digits'];
        }
        return "";
    }

    public function getTransactionId($lastTransaction)
    {
        if (!empty($lastTransaction['acquirer_tid'])) {
            return $lastTransaction['acquirer_tid'];
        }

        return "N/A";
    }

    public function getNSU($lastTransaction)
    {
        if (!empty($lastTransaction['acquirer_nsu'])) {
            return $lastTransaction['acquirer_nsu'];
        }

        return "N/A";
    }

    public function getAuthorizationCode($lastTransaction)
    {
        if (!empty($lastTransaction['acquirer_auth_code'])) {
            return $lastTransaction['acquirer_auth_code'];
        }

        return "N/A";
    }

    public function getAcquireMessage($lastTransaction)
    {
        if (!empty($lastTransaction['acquirer_message'])) {
            return $lastTransaction['acquirer_message'];
        }

        return "N/A";
    }

    public function getBoletoNumber($lastTransaction)
    {
        if (!empty($lastTransaction['nosso_numero'])) {
            return $lastTransaction['nosso_numero'];
        }

        return "";
    }

    public function getLine($lastTransaction)
    {
        if (!empty($lastTransaction['line'])) {
            return $lastTransaction['line'];
        }

        return "";
    }

    public function getLink($lastTransaction)
    {
        $helper = Mage::helper('paymentmodule');
        if (!empty($lastTransaction['url'])) {
            return
                "<a href='" . $lastTransaction['url'] . "' target='_blank'>" . $helper->__('Print boleto') . "</a><br>";
        }

        return "";
    }

}