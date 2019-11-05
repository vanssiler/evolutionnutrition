<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */
class Amasty_Scheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    const VARIEN_DATA_FORM_ELEMENT_CLASS_NAME_PREFIX = 'Varien_Data_Form_Element_';

    const CUSTOMATTR_DATA_FORM_ELEMENT_CLASS_NAME_PREFIX = 'Amasty_Customerattr_Block_Data_Form_Element_';

    const FIELD_PREFIX_ORDERATTR = 'oa_';
    const ORDERATTR_MODULE_NAME = 'Amasty_Orderattr';
    const CHECKOUTFEES_MODULE_NAME = 'Amasty_Checkoutfees';

    const FIELD_PREFIX_CUSTOMERATTR = 'ca_';
    const INPUT_NAME_PREFIX_CUSTOMERATTR = 'amcustomerattr';
    const CUSTOMERATTR_MODULE_NAME = 'Amasty_Customerattr';

    const MATCH_AMASTY_ATTRIBUTES_PATTERN = '/%s.*/';

    const MINIMUM_PASSWORD_LENGTH  = 6;

    protected $_location;
    protected $_checkoutSession;
    
    protected function _canUseMethod($quote, $method)
    {
        $billingCountry = $quote->getBillingAddress()->getCountry();
        if (!empty($billingCountry)
            && !$method->canUseForCountry($quote->getBillingAddress()->getCountry())) {
            return false;
        }

        if (!$method->canUseForCurrency($quote->getStore()->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }
        return true;
    }
    
    protected function _getPaymentMethods($quote)
    {   
        $store = $quote ? $quote->getStoreId() : null;
        $methods = Mage::helper('payment')->getStoreMethods($store, $quote);
        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        foreach ($methods as $key => $method) {
            if ($this->_canUseMethod($quote, $method)
                && ($total != 0
                    || $method->getCode() == 'free'
                    || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
            } else {
                unset($methods[$key]);
            }
        }
        
        return $methods;
    }
    
    public function getDefaultPeymentMethod($quote)
    {
        
        $ret = NULL;
        $default = Mage::getStoreConfig('amscheckout/default/payment_method');
        
        $paymentMethods = $this->_getPaymentMethods($quote);
        
        if ($default == 'none'){
            $ret = NULL;
        } else if ($default == ''){ //first available
            $paymentMethods = array_values($paymentMethods);

            if ($ret === NULL && isset($paymentMethods[0]))
                $ret = $paymentMethods[0]->getCode();
        } else{
            foreach($paymentMethods as $method){
                
                if ( $method->getCode() == $default ){
                    $ret = $default;
                    break;
                }
            }
        }

        if (!$ret && $default !== 'none'){
            $paymentMethods = array_values($paymentMethods);

            if ($ret === NULL && isset($paymentMethods[0]))
                $ret = $paymentMethods[0]->getCode();
        }

        return $ret;
    }
    
    public function getDefaultShippingMethod($quote)
    {
        $ret = NULL;
        $default = Mage::getStoreConfig('amscheckout/default/shipping_method');
        $first = NULL;
        $address = $quote->getShippingAddress();
        $address->collectShippingRates()->save();
        
        $_shippingRateGroups = $address->getGroupedAllShippingRates();
        
        foreach ($_shippingRateGroups as $code => $_rates){
            foreach ($_rates as $_rate){
                if ($default == $_rate->getCode()){
                    $ret = $default;
                    break;
                }
                
                if ($first === NULL)
                    $first = $_rate->getCode();
            }
        }
        
        if (!$ret){
            $ret = $first;
        }
        
        return $ret;
    }

    public function getDefaultCountry($shippingAddress = null)
    {
        $ret = NULL;
        
        if ($this->_useGeoIp()){

            if ($this->isAmastyGeoipInstalled()){
                $location = $this->_getGeipLocation();
                $ret = $location['country'];
            } else {
                $longIP = Mage::helper('core/http')->getRemoteAddr(true);

                $country = Mage::getModel('amscheckout/country');

                $countryCollection = $country->getCollection();

                $countryCollection->getSelect()->where("$longIP between ip_from and ip_to");

                $data = $countryCollection->getData();
                if (count($data) > 0)
                    $ret = $data[0]['code'];
            }

        }


        if (empty($ret)){
            $ret = Mage::getStoreConfig('amscheckout/default/country');
        }

        if (empty($ret) && $shippingAddress){

            $ret = $shippingAddress->getCountryId();
        }

        if (empty($ret)){
            $ret = Mage::getStoreConfig('general/country/default');
        }

        return $ret;
    }

    public function getCheckoutHeading()
    {
        return Mage::getStoreConfig('amscheckout/general/heading');
    }

    public function getCheckoutTitle()
    {
        return Mage::getStoreConfig('amscheckout/general/title');
    }

    public function getCheckoutDescription()
    {
        return Mage::getStoreConfig('amscheckout/general/description');
    }

    public function getDefaultRegion($allowNull = FALSE, $shippingAddress)
    {
        $ret = null;

//       if ($this->isAmastyGeoipInstalled()){
//           $location = $this->_getGeipLocation();
//           $ret = $location['region'];
//       }

        if (empty($ret) && $shippingAddress){
            $ret = $shippingAddress->getRegion();
        }

        if ($allowNull && $ret == '-'){
            $ret = "";
        }

        return $ret;
    }

    public function getDefaultRegionId($allowNull = FALSE, $shippingAddress)
    {
        $ret = null;

//        if ($this->isAmastyGeoipInstalled()){
//            $location = $this->_getGeipLocation();
//            $ret = $location['region'];
//        }

        if (empty($ret) && $shippingAddress){
            $ret = $shippingAddress->getRegionId();
        }

        if ($allowNull && $ret == '-'){
            $ret = "";
        }

        return $ret;
    }
    
    protected function _getGeipLocation()
    {
        if (!$this->_location) {


            if ($this->isAmastyGeoipInstalled()){
                $ip = Mage::helper('core/http')->getRemoteAddr();
//                $ip = '72.229.28.185';
//                $ip = '213.184.225.37';
                $location = Mage::getModel("amgeoip/geolocation")->locate($ip);

                if ($location) {
                    $this->_location = array(
                        'city' => $location->getCity(),
                        'country' => $location->getCountry(),
                        'postal_code' => $location->getPostalCode(),
                        'region' => $location->getRegion(),
                    );
                }


            } else {
                $longIP = Mage::helper('core/http')->getRemoteAddr(true);

                $block = Mage::getModel('amscheckout/block');

                $blockCollection = $block->getCollection();

                $blockCollection->getSelect()->join(
                        array(
                            'locations' => Mage::getSingleton('core/resource')->getTableName('amscheckout/location')
                        ), 'locations.geoip_loc_id = main_table.geoip_loc_id',
                        array('locations.city', 'locations.postal_code'));

                $blockCollection->getSelect()->where("$longIP between main_table.start_ip_num and main_table.end_ip_num");

                $data = $blockCollection->getData();

                if (count($data) > 0)
                    $this->_location = $data[0];
            }
        }
        return $this->_location;
    }
    
    public function getDefaultCity($allowNull = FALSE, $shippingAddress = null)
    {
        $ret = NULL;
        
        if ($this->_useGeoIp()){
            $location = $this->_getGeipLocation();
            $ret = $location['city'];
        }

        if ($ret == NULL && $shippingAddress){
            $ret = $shippingAddress->getCity();
        }



        if (empty($ret) && !$allowNull){
            $ret = '-';
        }

        if ($allowNull && $ret == '-'){
            $ret = "";
        }

        return $ret;
    }
        
    public function getDefaultPostcode($allowNull = FALSE, $shippingAddress = null)
    {
        $ret = NULL;
        if ($this->_useGeoIp()){
            $location = $this->_getGeipLocation();
            
            $ret = $location['postal_code'];
        }

        if ($ret == NULL && $shippingAddress){
            $ret = $shippingAddress->getPostcode();
        }

        if (empty($ret) && !$allowNull){
            $ret = '-';
        }

        if ($allowNull && $ret == '-'){
            $ret = "";
        }

        return $ret;
    }

    protected function _useGeoIp()
    {
        return (Mage::getModel('amscheckout/import')->isDone() && Mage::getStoreConfig('amscheckout/geoip/use') == 1) || $this->isAmastyGeoipInstalled();
    }

    public function getAreas()
    {
        $ret = array();
        $storeId = Mage::app()->getStore()->getStoreId();
        $areas = Mage::getModel("amscheckout/area")->getAreas($storeId, TRUE);
        
        foreach($areas as $area){
           $ret[$area['area_key']] = $area;
        }
        
        return $ret;
    }
    
    public function getFields($area)
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getModel("amscheckout/field")->getAreaFields($storeId, $area);
    }

    public function useBilling4Shipping()
    {
        return true;
    }

    public function getCheckoutUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/checkout',array('_secure'=>true));
    }

    public function getUpdateUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/update',array('_secure'=>true));
    }

    public function getRenderUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/render',array('_secure'=>true));
    }

    public function getSuccessUrl()
    {
        return Mage::getUrl('checkout/onepage/success', array('_secure'=>true));
    }

    public function getCouponUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/couponPost', array('_secure'=>true));
    }

    public function getCartUrl()
    {
         return Mage::getUrl('amscheckoutfront/onepage/cart',array('_secure'=>true));
    }

    public function getGiftCartUrl()
    {
         return Mage::getUrl('amscheckoutfront/onepage/giftcart',array('_secure'=>true));
    }

    public function getGiftCartCancelUrl(){
         return Mage::getUrl('amscheckoutfront/onepage/giftcartcancel',array('_secure'=>true));
    }

    public function getAmGiftCartUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/amgiftcart',array('_secure'=>true));
    }

    public function getAmGiftCartCancelUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/amgiftcartcancel',array('_secure'=>true));
    }
    
    public function getDeleteUrl()
    {
         return Mage::getUrl('amscheckoutfront/onepage/delete',array('_secure'=>true));
    }

    public function getCentinelUrl()
    {
        return Mage::getUrl('centinel/index/authenticationstart', array('_secure'=>true));
    }

    public function getAuthCentinelUrl()
    {
        return Mage::getUrl('amscheckoutfront/onepage/authcentinel', array('_secure'=>true));
    }

    public function getContinueShoppingUrl()
    {
        $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
        if (!$url) {
            $url = Mage::getUrl();
        }
        return $url; 
    }

    public function getBeforeControlHtml($_field, $repl = array(), $showLabel = TRUE)
    {
        $requred = ((isset($_field['field_required']) && $_field['field_required'] == 1) ? "<em>*</em>" : "");
        
        return strtr('<li class="amscheckout-row" style="width: '
            . (isset($_field['column_position']) ? $_field['column_position'] : '') . '%;'
            . (isset($_field['field_disabled']) && $_field['field_disabled'] ? 'display: none;' : '') . '"><div>
            '.($showLabel ? '<label for="' . (isset($_field['field_disabled']) ? $_field['field_key'] : '')
                .'" class="amscheckout-label">' .
                (isset($_field['field_label']) ? Mage::helper('core')->escapeHtml($_field['field_label']) : '')
                . $requred . '</label>' : '').'
            <div class="amscheckout-control">', $repl);
    }

    public function getAfterControlHtml($_field)
    {
        return '</div></div></li>';
    }

    public function getShippingCustomerWidget($block, $tpl)
    {

        return $block->getLayout()->createBlock('customer/widget_name')->
                setTemplate($tpl)->
                setObject($block->getAddress())->
                setFieldIdFormat('shipping:%s')->
                setFieldNameFormat('shipping[%s]')->
                setFieldParams('onchange="shipping.setSameAsBilling(false)"');
    }

    public function getBillingCustomerWidget($block, $tpl)
    {
        return $block->getLayout()->createBlock('customer/widget_name')->
            setTemplate($tpl)->
            setObject($block->getAddress()->getFirstname() ? $block->getAddress() : $block->getQuote()->getCustomer())->
            setForceUseCustomerRequiredAttributes(!$block->isCustomerLoggedIn())->
            setFieldIdFormat('billing:%s')->setFieldNameFormat('billing[%s]');
    }

    public function getAttributeValidationClass($field, $requred)
    {
        return $requred ? "required-entry" : "";
    }
    
    public function getConfigShippingRates($block)
    {
        $resultShippingRates = array();
        
        $defaultShippingRates = $block->getShippingRates();
        
        $shippingRates = array();
        
        foreach ($defaultShippingRates as $code => $rates){
            foreach ($rates as $rate){        
                $shippingRates['s_method_'.$rate->getCode()] = array(
                    "code" => $code,
                    "rate" => $rate
                );
            }
        }
        
        foreach($this->getFields("shipping_method") as $field){
            if (isset($shippingRates[$field['field_key']])){
                $shippingRates[$field['field_key']]['field'] = $field;
                $resultShippingRates[$field['field_key']] = $shippingRates[$field['field_key']];
            }
        }
        
        foreach($shippingRates as $key => $el){
            if (!isset($resultShippingRates[$key]))
                $el["field"] = array(
                    "field_label" => $el['rate']->getMethodTitle()
                );
                $resultShippingRates[$key] = $el;
        }
        
        return $resultShippingRates;
    }

    public function getConfigPaymentMethods($block)
    {
        $ret = array();
        $defaultMethods = $block->getMethods();
        $methods = array();
        
        $fields = $this->getFields("payment");
        
        foreach($defaultMethods as $method){
            $methods["p_method_" . $method->getCode()] = $method;
        }
        
        foreach($fields as $field){
            if (isset($methods[$field['field_key']])){
                $ret[] = array(
                    'field' => $field,
                    'method' => $methods[$field['field_key']]
                );
            }
        }
        
        return $ret;
    }

    public function isShoppingCartOnCheckout()
    {
        return Mage::getStoreConfig('amscheckout/shopping_cart/checkout');
    }

    public function isMergeShoppingCartCheckout()
    {
        return Mage::getStoreConfig('amscheckout/shopping_cart/cart_to_checkout');
    }

    public function isAllowGuestCheckout()
    {
        return Mage::getStoreConfig('checkout/options/guest_checkout');
    }

    public function isCustomerMustBeLogged()
    {
        return !$this->isAllowGuestCheckout() && Mage::getStoreConfig('checkout/options/customer_must_be_logged') == 1;
    }

    public function is3DSecureEnabled()
    {
        return (Mage::getStoreConfig('payment/authorizenet/centinel')
                || Mage::getStoreConfig('payment/paypal_direct/centinel')) == 1;
    }

    public function skipCouponSection()
    {
        return Mage::getStoreConfig('amscheckout/sections/coupon') != 1;
    }

    public function skipGiftCardSection()
    {
        return Mage::getStoreConfig('amscheckout/sections/giftcard') != 1;
    }

    public function skipAmGiftCardSection()
    {
        return Mage::getStoreConfig('amscheckout/sections/amgiftcard') != 1;
    }

    public function getBillingUpdatable()
    {
        $ret = array();
        
        $updatable = explode(",", Mage::getStoreConfig('amscheckout/update/shipping'));
        
        foreach($updatable as $field){
            
            switch ($field){
                case "address":
                    $ret[] = "billing:street1";
                    for ($_i = 2, $_n = Mage::helper('customer/address')->getStreetLines(); $_i <= $_n; $_i++){
                        $ret[] = "billing:street" . $_i;
                    }
                break;
                case "city":
                    $ret[] = "billing:city";
                break;
                case "region":
                    $ret[] = "billing:region_id";
                    $ret[] = "billing:region";
                break;
                case "postcode":
                    $ret[] = "billing:postcode";
                break;
                case "country":
                    $ret[] = "billing:country_id";
                break;
            }
        }
        return $ret;
    }

    public function getShippingUpdatable()
    {
        $ret = array();
        
        $updatable = explode(",", Mage::getStoreConfig('amscheckout/update/shipping'));
        
        foreach($updatable as $field){
            
            switch ($field){
                case "address":
                    $ret[] = "shipping:street1";
                    for ($_i = 2, $_n = Mage::helper('customer/address')->getStreetLines(); $_i <= $_n; $_i++){
                        $ret[] = "shipping:street" . $_i;
                    }
                break;
                case "city":
                    $ret[] = "shipping:city";
                break;
                case "region":
                    $ret[] = "shipping:region_id";
                    $ret[] = "shipping:region";
                break;
                case "postcode":
                    $ret[] = "shipping:postcode";
                break;
                case "country":
                    $ret[] = "shipping:country_id";
                break;
            }
        }
        return $ret;
    }

    public function reloadAfterShippingMethodChanged()
    {
        return Mage::getStoreConfig('amscheckout/update/shipping_methods');
    }

    /**
     * Check whether billing and shipping addresses are the same.
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return bool
     */
    public function isSameAddresses($quote)
    {
        if (!$quote->getCustomerId()) {
            return true;
        }

        $customer = $quote->getCustomer();

        return (!$customer->getDefaultShippingAddress() || $quote->getShippingAddress()->getSameAsBilling())
            || $customer->getDefaultBillingAddress() && $customer->getDefaultShippingAddress()
            && $customer->getDefaultBillingAddress()->getId() == $customer->getDefaultShippingAddress()->getId();
    }

    public function reloadPaymentShippingMethodChanged()
    {
        return Mage::getStoreConfig('amscheckout/update/payment_methods');
    }

    /**
     * @return bool
     */
    public function isCheckoutFeesEnabled()
    {
        return $this->isModuleEnabled(self::CHECKOUTFEES_MODULE_NAME);
    }

    public function initAddress($block)
    {
        if (!$block->isCustomerLoggedIn()) {
            $blockAddress = $block->getAddress();

            $shippingAddress = $block->getQuote()->getShippingAddress();

            if ($shippingAddress->getCountryId() && !$blockAddress->getCountryId()){
                $blockAddress->setCountryId($shippingAddress->getCountryId());
            }

            if ($shippingAddress->getRegionId() !== '-' && $shippingAddress->getRegionId() && !$blockAddress->getRegionId()){
                $blockAddress->setRegionId($shippingAddress->getRegionId());
            }

            if ($shippingAddress->getRegion() !== '-' && $shippingAddress->getRegion() && !$blockAddress->getRegion()){
                $blockAddress->setRegion($shippingAddress->getRegion());
            }

            if ($shippingAddress->getCity() !== '-' && $shippingAddress->getCity() && !$blockAddress->getCity()){
                $blockAddress->setCity($shippingAddress->getCity());
            }

            if ($shippingAddress->getPostcode() !== '-' && $shippingAddress->getPostcode() && !$blockAddress->getPostcode()){
                $blockAddress->setPostcode($shippingAddress->getPostcode());
            }
        } else {
            $blockAddress = $block->getAddress();

           $shippingAddress = $block->getQuote()->getShippingAddress();

            if ($shippingAddress->getCity() == '-'){
                $blockAddress->setCity("");
            }

            if ($shippingAddress->getPostcode() == '-'){
                $blockAddress->setPostcode("");
            }
        }

        if ($shippingAddress->getTelephone() == '-'){
            $blockAddress->setTelephone("");
        }

        if ($shippingAddress->getRegion() == '-'){
               $blockAddress->setRegion("");
           }

        if ($block instanceof Mage_Checkout_Block_Onepage_Shipping){

            $this->initPaymentMethod($block->getQuote());

            if ( ! Mage::helper("amscheckout")->isQuickFirstLoad()){
                $block->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            }
        }
    }

    public function initPaymentMethod($quote, $initPaymentMethod = false)
    {
        try{
            $quote->getPayment()->getMethodInstance();
            $initPaymentMethod = true;
        } catch (Exception $e){

        }

        if ($initPaymentMethod) {
            try{
                $payment = $quote->getPayment();
                $payment->importData(array("method" => $this->getDefaultPeymentMethod($quote)));
            } catch (Exception $e){

            }
        }
    }

    public function getLayoutType()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $type = Mage::getModel("amscheckout/config")->getLayoutType($storeId)->value;
        
        return $type;
    }

    public function isMobile()
    {  
        $regex_match = "/(nokia|iphone|ipad|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|"  
                     . "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|"  
                     . "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|"  
                     . "symbian|smartphone|mmp|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|"  
                     . "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220"  
                     . ")/i";  

        if (preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']))) {  
            return TRUE;  
        }  

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== -1) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            return TRUE;  
        }      

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));  
        $mobile_agents = array(  
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
            'wapr','webc','winw','winw','xda ','xda-');  

        if (in_array($mobile_ua,$mobile_agents)) {  
            return TRUE;  
        }  

        if (isset($_SERVER['ALL_HTTP']) && strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') !== -1) {
            return TRUE;  
        }  

        $showDesktop = $this->_customerSession()->getShowDesktop(); //AW VARIABLE
        if ($showDesktop === FALSE){
            return TRUE;  
        }

        return FALSE;  
    } 
    
    protected function _customerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function showThumbnail()
    {
        return Mage::getStoreConfig('amscheckout/visual/show_thumbnail');
    }

    public function showNewsletter()
    {
        return Mage::getStoreConfig('amscheckout/visual/show_newsletter');
    }

    public function getThumbnailSize()
    {
        return Mage::getStoreConfig('amscheckout/visual/thumbnail_size');
    }
    
    public function getCheckoutSession()
    {
        if (null === $this->_checkoutSession) {
            $this->_checkoutSession = Mage::getSingleton('checkout/session');
        }
        return $this->_checkoutSession;
    }
    
    public function getQuoteItemMessages($quoteItem)
    {
        $messages = array();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array(
                    'text' => $message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }

        // Add messages saved previously in checkout session
        $checkoutSession = $this->getCheckoutSession();
        if ($checkoutSession) {
            /* @var $collection Mage_Core_Model_Message_Collection */
            $collection = $checkoutSession->getQuoteItemMessages($quoteItem->getId(), true);
            if ($collection) {
                $additionalMessages = $collection->getItems();
                foreach ($additionalMessages as $message) {
                    /* @var $message Mage_Core_Model_Message_Abstract */
                    $messages[] = array(
                        'text' => $message->getCode(),
                        'type' => ($message->getType() == Mage_Core_Model_Message::ERROR) ? 'error' : 'notice'
                    );
                }
            }
        }

        return $messages;
    }

    /**
     * @return string
     */
    public function getIwaysPayPalMethodCode()
    {
        $isEnbIwaysPayPalPlus = Mage::helper('core')->isModuleEnabled("Iways_PayPalPlus");
        $iwaysPayPalPlusMehtodCode = ($isEnbIwaysPayPalPlus) ? Iways_PayPalPlus_Model_Payment::METHOD_CODE : "none";

        return $iwaysPayPalPlusMehtodCode;
    }

    /**
     * @return string
     */
    public function getValidateUrlIwaysPayPal()
    {
        $isEnbIwaysPayPalPlus = Mage::helper('core')->isModuleEnabled("Iways_PayPalPlus");
        return ($isEnbIwaysPayPalPlus) ? Mage::helper('iways_paypalplus')->getUrl('paypalplus/index/validate') : "";
    }

    /**
     * @param $prefix
     * @return mixed
     */
    protected function getModuleNameByAttributePrefix($prefix)
    {
        $extraFields = array(
            self::CUSTOMERATTR_MODULE_NAME => self::FIELD_PREFIX_CUSTOMERATTR,
            self::ORDERATTR_MODULE_NAME => self::FIELD_PREFIX_ORDERATTR
        );

        return array_search($prefix, $extraFields);
    }

    public function isAmastyCouponsInstalled()
    {
        return (string)Mage::getConfig()->getNode('modules/Amasty_Coupons/active') == 'true';
    }

    public function isAmastyGeoipInstalled()
    {
        return (string)Mage::getConfig()->getNode('modules/Amasty_Geoip/active') == 'true';
    }

    public function isQuickFirstLoad()
    {
        return Mage::getStoreConfig('amscheckout/update/quick_load');
    }

    public function _prepareLayoutBeforeUpdate($id, $update)
    {
        if ($id == 'checkout_onepage_review') {

            $payment = Mage::getSingleton('checkout/type_onepage')
                        ->getQuote()
                        ->getPayment();
            try{
                $payment->getMethodInstance();
            } catch (Exception $e){
                $update->addUpdate('<remove name="payment.form.directpost"/>');
            }

        } else if($id == 'checkout_onepage_paymentmethod'){

            if (!$this->skipGiftCardSection() || $this->isShoppingCartOnCheckout())
                $update->addUpdate('<remove name="giftcardaccount_additional"/>');
        }

        // fix an error on the second checkout attempt if paid with paypal hosted pro (with a card)
        $update->addUpdate('<remove name="paypal.iframe"/>');
    }

    public function getProduct($item)
    {
        return $item->getProduct();
    }

    public function getChildProduct($item)
    {
        if ($option = $item->getOptionByCode('simple_product')) {
            return $option->getProduct();
        }
        return $this->getProduct($item);
    }

    public function getProductThumbnail($item)
    {
        $product = $this->getChildProduct($item);
        if (!$product || !$product->getData('thumbnail')
            || ($product->getData('thumbnail') == 'no_selection')
            || (Mage::getStoreConfig('checkout/cart/configurable_product_image') == 'parent')) {
            $product = $this->getProduct($item);
        }
        return Mage::helper('catalog/image')->init($product, 'thumbnail');
    }

    protected function getHandles($id)
    {
        $handles = array($id);

        switch ($id) {
            case 'checkout_onepage_review':
                $handles[] = 'amscheckout_onepage_review_handle';
                break;
        }

        return $handles;
    }

    /**
     * @param $id
     * @return string
     */
    public function getLayoutHtml($id){
        $layout =  Mage::app()->getLayout();
        $layout->getUpdate()->setCacheId(uniqid("amscheckout_".$id));
        $update = $layout->getUpdate();

        $update->load($this->getHandles($id));
        $this->_prepareLayoutBeforeUpdate($id, $update);
        $layout->generateXml();
        $layout->generateBlocks();

        Mage::dispatchEvent(
            'amscheckout_get_layout_html_after',
            array(
                'handle_id' => $id,
                'layout' => $layout
            )
        );

        return $layout->getOutput();
    }

    public function checkPassword($password)
    {
        $isCorrect = true;

        if (isset($password) && strlen($password)
            && (Mage::helper('core/string')->strlen($password) < Mage_ImportExport_Model_Import_Entity_Customer::MAX_PASSWD_LENGTH)
        ) {
            $isCorrect = false;
        }

        return $isCorrect;
    }

    public function getPasswordLengthMessage()
    {
        $reflectClass = new ReflectionClass('Mage_Customer_Model_Customer');
        $minPasswLen  = ($reflectClass->hasConstant('MINIMUM_PASSWORD_LENGTH')) ? Mage_Customer_Model_Customer::MINIMUM_PASSWORD_LENGTH : self::MINIMUM_PASSWORD_LENGTH;
        return $this->__('The minimum password length is') . ' ' . $minPasswLen;
    }

    public function moveToCheckoutHead()
    {
        $moveToHead = false;

        if (Mage::helper('core')->isModuleEnabled('Amasty_Promo') && $this->isShoppingCartOnCheckout()) {
            $moveToHead = true;
        }

        return $moveToHead;
    }

    /**
     * @param $field
     * @return string
     */
    protected function getAttributeId($field)
    {
        return $field['area_key'] . ':' . $field['field_key'];
    }

    /**
     * Retrieve url of skins file
     *
     * @param   string $file path to file in skin
     * @param   array $params
     * @return  string
     */
    public function getSkinUrl($file = null, array $params = array())
    {
        return Mage::getDesign()->getSkinUrl($file, $params);
    }

    /**
     * @return string
     */
    public function getAmSocialLoginHtml()
    {
        $html = '';
        if (Mage::helper('core')->isModuleEnabled('Amasty_Ajaxlogin')) {
            $block = $this->getLayout()->createBlock('amajaxlogin/customer_account_dashboard_social')
                ->setTemplate('amasty/amajaxlogin/customer/account/dashboard/social.phtml');
            if ($block) {
                $html = $block->toHtml();
            }
        }

        return $html;
    }
}
