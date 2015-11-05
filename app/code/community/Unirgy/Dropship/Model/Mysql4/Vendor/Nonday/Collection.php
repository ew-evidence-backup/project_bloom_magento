<?php
class Unirgy_Dropship_Model_Mysql4_Vendor_Nonday_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('udropship/vendor_nonday');
        parent::_construct();
    }
}