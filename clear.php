<?php
      //increase execution time
      ini_set('max_execution_time', 900); //900 seconds = 15 minutes
 
      //require Magento
      require_once 'app/Mage.php';
      $app = Mage::app('admin');
      umask(0);
 
      //enable Error Reporting
      error_reporting(E_ALL & ~E_NOTICE);
 
      $types=array('config','layout','block_html','translate','collections','eav','config_api','config_api2');
      foreach($types as $type) {
            $c = Mage::app()->getCacheInstance()->cleanType($type);
            Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type));
      }
?>