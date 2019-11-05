<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Model_Geolocation extends Varien_Object
{
    public function locate($ip)
    {
        if (!empty($ip)) {
            $ip = str_replace(' ', '', $ip);
            $ip = explode(',', $ip);
            $ip = $ip[0];
            $isIpv6 = (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

            //$ip = '213.184.225.37';//Minsk
            if ($isIpv6) {
                $ip = substr($ip, 0, strrpos($ip, ":")) . ':0'; // Mask IP according to EU GDPR law
                $longIP = Mage::helper('amgeoip')->getLongIpV6($ip);
                $blockTable = 'amgeoip/block_v6';
            } else {
                $ip = substr($ip, 0, strrpos($ip, ".")) . '.0'; // Mask IP according to EU GDPR law
                $longIP = sprintf("%u", ip2long($ip));
                $blockTable = 'amgeoip/block';
            }

            /* @var Amasty_Geoip_Model_Import $geoIpModel */
            $geoIpModel = Mage::getModel('amgeoip/import');
            if ($geoIpModel->isDone()) {
                $db = Mage::getSingleton('core/resource')->getConnection('core_read');
                $blockSelect = $db->select()
                    ->from(Mage::getSingleton('core/resource')->getTableName($blockTable))
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array('geoip_loc_id', 'latitude', 'longitude'))
                    ->where('start_ip_num <= ?', $longIP)
                    ->order('start_ip_num DESC')
                    ->limit(1);

                $select = $db->select()
                    ->from(array('b' => $blockSelect))
                    ->joinInner(
                        array('l' => Mage::getSingleton('core/resource')->getTableName('amgeoip/location')),
                        'l.geoip_loc_id = b.geoip_loc_id',
                        null
                    )
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns(array('l.*', 'b.latitude', 'b.longitude'));

                if ($res = $db->fetchRow($select))
                    $this->setData($res);
            }
        }

        return $this;
    }
}
