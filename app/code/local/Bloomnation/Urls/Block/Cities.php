<?php
    class Bloomnation_Urls_Block_Cities extends Mage_Core_Block_Template {
        public function __construct() {
            $perPage = 200;
            $page = (int)Mage::app()->getRequest()->getParam('page',0);
            Mage::getSingleton('urls/cities')->loadCities($perPage,$page);
        }
        
        public function getCityList() {
            // For the sake of speed execute straight SQL query
            $result = Mage::getSingleton('urls/cities')->getCities();
            
            return $result;
        }
        
        public function getCacheKeyInfo() {
            $info = array(
                'Mage_Urls_Block_Cities',
                'page'=>(int)Mage::app()->getRequest()->getParam('page',0)
            );
        }
    }
?>