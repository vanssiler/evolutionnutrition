<?php

class Mundipagg_Paymentmodule_I18nController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('paymentmodule/exception')->initExceptionHandler();
    }

    public function getTableAction()
    {
        $translateTable = Mage::app()->getTranslator()->getData();

        //ignore lines starting with # in the translate.csv file
        $translateTable = array_filter($translateTable,function($line) {
            return $line[0] !== '#';
        });

        //fix encoding errors;
        $parsed = [];
        array_walk($translateTable, function (&$line, $index) use (&$parsed) {

            json_encode($line);
            if (json_last_error() !== 0) {
                $line = utf8_encode(mb_convert_encoding($line, 'UTF-8',"UTF-8"));
            }
            json_encode($index);
            if (json_last_error() !== 0) {
                $index = utf8_encode(mb_convert_encoding($index, 'UTF-8',"UTF-8"));
            }
            $parsed[$index] = $line;
        });

        $json = json_encode($parsed,JSON_FORCE_OBJECT);

        return $this->getResponse()
            ->clearHeaders()
            ->setHeader('HTTP/1.0', 200 , true)
            ->setHeader('Content-Type', 'text/html')
            ->setBody(json_encode($json));
    }
}
