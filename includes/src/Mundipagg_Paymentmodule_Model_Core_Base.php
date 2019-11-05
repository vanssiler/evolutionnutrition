<?php

/**
 * Class Mundipagg_Paymentmodule_Model_Core_Charge
 */
class Mundipagg_Paymentmodule_Model_Core_Base
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $methodName = $this->fromSnakeToCamel($name);
        if (!method_exists($this, $methodName)) {
            throw new \Exception('UNKNOWN WEBHOOK ACTION');
        }

        return $this->{$methodName}($arguments[0]);
    }

    /**
     * @param string $snake
     * @return string
     */
    protected function fromSnakeToCamel($snake)
    {
        $result = array();
        $length = strlen($snake);

        for ($i = 0; $i < $length ; $i++) {
            if ($snake[$i] === '_') {
                $result[] = ucfirst($snake[++$i]);
            } else {
                $result[] = $snake[$i];
            }
        }
        
        return implode($result);
    }

    protected function getPaymentMethodModel($paymentMethod)
    {
        $model = '';

        switch ($paymentMethod) {
            case 'credit_card':
                $model = Mage::getModel('paymentmodule/creditcard');
                break;
            case 'boleto':
                $model = Mage::getModel('paymentmodule/boleto');
                break;
        }

        return $model;
    }
}
