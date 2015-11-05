<?php

class Unirgy_Dropship_Block_Grandtotal extends Mage_Tax_Block_Checkout_Grandtotal {
    protected $_template = 'tax/checkout/grandtotal.phtml';

    public function getTotalShipmentCost() {
        return Mage::helper('udropship/data')->getDeliveryFromQuoteVendors();

        $priceChangeAmount = 0;
        
        foreach($this->getTotal()->getAddress()->getAllItems() as $item){
            $vendorsArr[] = $item->getUdropshipVendor();
        }
        $vendorsArr = array_unique($vendorsArr);
        $uhelper = Mage::helper('udropship');
        foreach($vendorsArr as $vendor) {
            $_v = $uhelper->getVendor($vendor);
            $priceChangeAmount += $_v->getDeliveryCharge();
        }
        
        return $priceChangeAmount;
    }
}