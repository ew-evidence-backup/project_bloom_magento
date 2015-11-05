<?php

class Bloomnation_Urls_Model_Quote_Item extends Mage_Sales_Model_Quote_Item {
    
    public function getUdropshipVendor() {
        return $this->getProduct()->getUdropshipVendor();
    }
}