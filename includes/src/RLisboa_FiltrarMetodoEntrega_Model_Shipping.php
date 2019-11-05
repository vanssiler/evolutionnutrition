<?php
/**
 * @category   RLisboa
 * @package    RLisboa_FiltrarMetodoEntrega
 * @author     falecomigo@eurafaellisboa.com.br
 * @website    https://eurafaellisboa.com.br
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 	*/
class RLisboa_FiltrarMetodoEntrega_Model_Shipping extends Mage_Shipping_Model_Shipping
{
    public function collectCarrierRates($carrierCode, $request)
    {
        if (!$this->_checkCarrierAvailability($carrierCode, $request)) {
            return $this;
        }
        return parent::collectCarrierRates($carrierCode, $request);
    }

	protected function _checkCarrierAvailability($carrierCode, $request = null)
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
      // Get group Id
      $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
      //Get customer Group name
      $group = Mage::getModel('customer/group')->load($groupId);
		$cd = $group->getCode();
		} else {
			$cd="";
		}
		
      if (!$cd == 'teste') {
            if ($carrierCode == 'flatrate') {
                # Hide Flat Rate
               
                return false;
            } elseif ($carrierCode == 'freeshipping') {
				  return false;
			}
        }
        return true;
		
		
		return true;
		
	}
}