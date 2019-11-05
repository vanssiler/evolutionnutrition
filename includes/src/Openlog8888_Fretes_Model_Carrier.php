<?php
class Openlog_Fretes_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'openlog_fretes';
    public function collectRates(Mage_Shipping_Model_Rate_Request $request){   
        $destPostcode = $request->getDestPostcode();
        $cep = $request->getDestPostcode();
        $peso = $request->getPackageWeight();
        $valor = $request->getPackageValue();
        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        
        $api = json_decode($this->getValorFreteAPI($valor, $peso, $email, $cep, $this->getAtributos()));
        
        
        if (!Mage::getStoreConfig('carriers/fretes/active')) {
            return false;
        }
        if($api == null || strtolower($api->{"atende"}) !== "sim"){
            return;
        }else{
            $result = Mage::getModel('shipping/rate_result');
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier('openlog_fretes');
            $method->setCarrierTitle($api->{'transportadora'});
            $method->setMethod('openlog_fretes');
            if($api->{'prazo'} == 1){
                $method->setMethodTitle($api->{'transportadora'}." (Prazo: ".$api->{'prazo'}." dia útil.)");
            }else{
                $method->setMethodTitle($api->{'transportadora'}." (Prazo: ".$api->{'prazo'}." dias úteis.)");
            }
            $method->setPrice($api->{'frete'});
            $result->append($method);
            return $result;
        }
    }
    public function getValorFreteAPI($valor, $peso, $email, $cep, $produtos){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://openlog.com.br/financeiro/api/Fretes/CalcularFrete",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => 'valor='.$valor.'&peso='.$peso.'&email='.$email.'&cep='.$cep."&produtos=".$produtos
        ));
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($httpcode == 200){
            return $response;
        }
    }

    public function getAtributos(){
        $items =Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $arr_id_items = array();

        foreach($items as $item) {
            $obj->id = $item->getProductId();
            $obj->nome =$item->getName();
            $obj->quantidade= $item->getQty();
            $obj->preco= $item->getPrice();

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $attributes = $product->getAttributes();

            foreach($attributes as $attribute){
                $attributeLabel = $attribute->getAttributeCode();
                $value = $attribute->getFrontend()->getValue($product);
                $obj->atributos->$attributeLabel = $value;      
            }

           array_push($arr_id_items, $obj);

        }
        
        return json_encode($arr_id_items); 
    }

    public function getAllowedMethods(){
        return array('openlog_fretes' => 'Padrão');
    }
    public function isTrackingAvailable()
    {
        return true;
    }

    public function getTrackingInfo($tracking)
    {
        $url = Mage::getStoreConfig('carriers/fretes/linkrastreio');

        $track = Mage::getModel('shipping/tracking_result_status');
        $track->setUrl($url . $tracking)
            ->setTracking($tracking)
            ->setCarrierTitle($this->getConfigData('name'));
        return $track;
    }
}