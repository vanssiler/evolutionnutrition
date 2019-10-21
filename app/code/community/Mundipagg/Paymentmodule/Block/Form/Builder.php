<?php

class Mundipagg_Paymentmodule_Block_Form_Builder extends Mundipagg_Paymentmodule_Block_Base
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paymentmodule/form/builder.phtml');
        $this->setMethodTitle('');
    }

    public function getMethodTitle()
    {
        $paymentName = $this->getModel()->getPaymentTitle();
        return $paymentName;
    }

    public function getStructure()
    {
        return $this->getModel()->getPaymentStructure();
    }
    
    private function getModel()
    {
        $methodCode = $this->getMethodCode();
        $model = $this->getModelName($methodCode);
        if (!$model) {
            // @todo think about exception
            // @todo log it
        }
        return $model;
    }

    private function getModelName($code)
    {
        $code = explode('_', $code);
        return Mage::getModel('paymentmodule/' . end($code));
    }

    public function getPartialHTML($element,$parentElement = '')
    {
        $grandTotal = $this->getGrandTotal();
        $retn = $this->getLayout();
        $data = array(
            'code' => $this->getMethodCode(),
            'element_index' => $this->getIndexFor($element),
            'show_value_input' => count($this->getStructure()) > 1,
            'grand_total' => number_format($grandTotal, "2", ",", ""),
            'parent_element' => $parentElement
            );
        if ($parentElement !== '') {
            $data ['parent_index'] = $this->getElementCount()[$parentElement];
        }

        $retn = $retn->createBlock(
            "paymentmodule/form_partial_$element",
            '',
            $data
        );

        $retn = $retn->toHtml();

        return $retn;
    }

    public function getMultiBuyerHtml($parentElement)
    {
        /**
         * @var Mundipagg_Paymentmodule_Model_Config_Multibuyer $multiBuyerConfig;
         */
        $multiBuyerConfig = Mage::getModel('paymentmodule/config_multibuyer');
        if (!$multiBuyerConfig->isEnabled()) {
            return '';
        }
        return $this->getPartialHTML('multibuyer',$parentElement);
    }

    public function getIndexFor($element)
    {
        $elementCount = $this->getElementCount();

        if ($elementCount == null) {
           $elementCount = array();
        }

        if (!isset($elementCount[$element])) {
            $elementCount[$element] = 0;
        }

        $elementCount[$element]++;
        $this->setElementCount($elementCount);

        return $this->elementCount[$element];
    }

    public function getGrandTotal()
    {
        $checkout = Mage::getSingleton('checkout/session');
        $grandTotal = $checkout->getQuote()->getGrandTotal();

        return $grandTotal;
    }

    public function toCurrencyFormat($amount)
    {
        $moneyHelper = Mage::helper('paymentmodule/monetary');
        return $moneyHelper->toCurrencyFormat($amount);
    }
}
