<?php
class Unirgy_Dropship_Model_Mysql4_Vendor_Nondate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('udropship/vendor_nondate');
        parent::_construct();
    }
}