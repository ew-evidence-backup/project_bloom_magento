<?php

class Unirgy_Dropship_Block_Manageproducts extends Mage_Core_Block_Template {
    public function getVendorProducts() {
        return Mage::getSingleton('udropship/session')->getVendor()->getAssociatedProducts();
    }
}