<?php

class Mundipagg_Paymentmodule_MultibuyerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Return json with a collection of regions
     * @return json Region Collection
     */
    public function getRegionsAction()
    {
        $countryCode = $this->getRequest()->getParam('country_id');
        $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('HTTP/1.0', 200 , true)
            ->setHeader('Content-Type', 'text/html')
            ->setBody(json_encode($regionCollection));
    }
}
