<?php

class Impl_Custom_Block_Tooltip extends Mage_Core_Block_Template {
    
    protected function _toHtml() {
        $limit = 5;
        $search = addslashes($this->getQuery());
        
        $query = "SELECT CONCAT(primary_city, ', ', state) as tooltip
                        FROM zip_codes_database
                        WHERE primary_city LIKE '%$search%'
                        GROUP BY primary_city, state
                        LIMIT 0,$limit";

        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);
        if(sizeof($result) <= 0) {
                $query = "SELECT vendor_name as tooltip
                                FROM udropship_vendor
                                WHERE vendor_name LIKE '%$search%'
                                LIMIT 0,$limit";
                $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);            
        }
        
        return $result;
    }
    
}
