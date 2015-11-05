<?php

class Unirgy_Dropship_Model_Mysql4_Vendor_Coupon extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('udropship/vendor_coupon', 'vendor_coupon_id');
    }
}
