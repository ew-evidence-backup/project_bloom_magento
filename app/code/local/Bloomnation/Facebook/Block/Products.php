<?php

class Bloomnation_Facebook_Block_Products extends Mage_Core_Block_Template {
    
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 1800
        ));
    }
    
    public function getCacheKeyInfo() {
        $cache = array();
        
        $cache['tname'] = 'Bloomnation_Facebook_Block_Products';
        if(Mage::registry('current_category')) {
            $cache['cat_id'] = Mage::registry('current_category')->getId();
        }
        
        return $cache;
    }
}