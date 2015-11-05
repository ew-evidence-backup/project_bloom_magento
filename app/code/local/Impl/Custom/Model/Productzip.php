<?php
class Impl_Custom_Model_Productzip extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('custom/productzip');
    }
	
}