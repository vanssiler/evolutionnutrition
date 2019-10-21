<?php

/**
 * Class Mundipagg_Paymentmodule_Model_Core_Charge
 */
class Mundipagg_Paymentmodule_Model_Core_Charge extends Mundipagg_Paymentmodule_Model_Core_Base
{
    /**
     * @param $charge
     * @throws Exception
     */
    protected function created($charge)
    {
        $helper = $this->getHelper();

        if (
         !$helper->isChargeAlreadyUpdated($charge->id, $charge->code, __FUNCTION__)
        ) {
            $helper->updateChargeInfo(__FUNCTION__, $charge);
        }
    }

    /**
     * @param $charge
     * @throws Exception
     */
    protected function pending($charge)
    {
        $this->created($charge);
    }

    /**
     * @param $charge
     */
    protected function paid($charge)
    {
        $helper = $this->getHelper();
        $helper->paidMethods(__FUNCTION__, $charge);
    }

    /**
     * @param $charge
     */
    protected function overpaid($charge)
    {
        $helper = $this->getHelper();
        $helper->paidMethods(__FUNCTION__, $charge);
    }

    /**
     * @param $charge
     */
    protected function underpaid($charge)
    {
        $helper = $this->getHelper();
        $helper->paidMethods(__FUNCTION__, $charge);
    }

    /**
     * @param $charge
     */
    protected function canceled($charge)
    {
        $helper = $this->getHelper();
        $helper->canceledMethods(__FUNCTION__, $charge);
    }

    /**
     * Same as canceled
     * @param $charge
     */
    protected function refunded($charge)
    {
        $this->canceled($charge);
    }

    /**
     * Same as canceled
     * @param $charge
     */
    protected function failed($charge)
    {
        $this->canceled($charge);
    }

    /**
     * Same as canceled
     * @param $charge
     * @throws Varien_Exception
     */
    protected function paymentFailed($charge)
    {
        $helper = $this->getHelper();
        $orderEnum = Mage::getModel('paymentmodule/enum_orderhistory');

        $helper
            ->canceledMethods(
            __FUNCTION__,
            $charge,
            $orderEnum->notAuthorized()
        );
    }

    /**
     * @param $charge
     * @throws Varien_Exception
     */
    protected function partialRefunded($charge)
    {
        $helper = $this->getHelper();
        $orderEnum = Mage::getModel('paymentmodule/enum_orderhistory');
        $helper
            ->canceledMethods(
                __FUNCTION__,
                $charge,
                $orderEnum->chargeRefunded()
            );
    }

    /**
     * @param $charge
     * @throws Varien_Exception
     */
    protected function partialCanceled($charge)
    {
        $helper = $this->getHelper();
        $orderEnum = Mage::getModel('paymentmodule/enum_orderhistory');

        $helper->canceledMethods(
            __FUNCTION__,
            $charge,
            $orderEnum->chargePartialCanceled()
        );
    }

    protected function getHelper()
    {
        return Mage::helper('paymentmodule/chargeoperations');
    }
}