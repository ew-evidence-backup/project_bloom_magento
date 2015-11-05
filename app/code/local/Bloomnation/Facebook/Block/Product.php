<?php

class Bloomnation_Facebook_Block_Product extends Mage_Catalog_Block_Product_Abstract {

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 1800
        ));
    }
    
    public function getCacheKeyInfo() {
        $cache = array();
        
        $cache['tname'] = 'Bloomnation_Facebook_Block_Products';
        if(Mage::registry('current_product_id')) {
            $cache['product_id'] = Mage::registry('current_product_id');
        }
        
        return $cache;
    }

}