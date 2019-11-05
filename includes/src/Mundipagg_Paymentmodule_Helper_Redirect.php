<?php

/**
 * Class Mundipagg_Paymentmodule_Helper_Redirect
 */
class Mundipagg_Paymentmodule_Helper_Redirect extends Mage_Core_Helper_Abstract
{
    public function orderSuccess()
    {
        $this->redirect('checkout/onepage/success');
    }

    public function orderFailure()
    {
        $this->redirect('checkout/onepage/failure');
    }

    private function redirect($route)
    {
        Mage::app()->getFrontController()
            ->getResponse()
            ->setRedirect(Mage::getUrl($route));
    }
}