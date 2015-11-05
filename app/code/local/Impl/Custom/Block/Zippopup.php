<?php
class Impl_Custom_Block_Zippopup extends Mage_Catalog_Block_Product_Abstract
{
	public function showDialog()
	{
		$zipcode = Mage::registry('bloom_nations_zipcode');
		
		if(isset($_COOKIE["bloom_nations_zipcode"])) {
			$zipcode = (Mage::registry('bloom_nations_zipcode')) ?: $_COOKIE["bloom_nations_zipcode"];
		}
		
		
		if(	($this->getRequest()->getModuleName() == 'catalog' && $this->getRequest()->getControllerName() == 'category' && $this->getRequest()->getActionName() == 'view')
			||
			($this->getRequest()->getModuleName() == 'catalog' && $this->getRequest()->getControllerName() == 'product')
		) {
			if(!isset($zipcode) || empty($zipcode)) {
				return true;				
			}
		}

		return false;
	}
	
	public function showInfo()
	{
		$zipcode = Mage::registry('bloom_nations_zipcode');
		
		if(isset($_COOKIE["bloom_nations_zipcode"])) {
			$zipcode = (Mage::registry('bloom_nations_zipcode')) ?: $_COOKIE["bloom_nations_zipcode"];
		}		
		
		if(!isset($zipcode) || empty($zipcode))
		{
			return false;
		}
		return true;
	}
	
	public function getInfo()
	{
		$result = Mage::registry('bloom_nations_zipcode_info');
		if(empty($result)) {
			$result = (Mage::registry('bloom_nations_zipcode_info')) ?: $_COOKIE["bloom_nations_zipcode_info"];
		} elseif(!isset($result)) {
			$result = 'Prepared by Local Florist';
		}
		return $result;
	}
	
	public function showDefault()
	{
		if(Mage::registry('catalog_status') == 'empty' || $_SESSION['nolocal'] == true)
		{
			return true;
		}
		return false;
	}
	
	public function showCloseButton()
	{
		if($this->getRequest()->getModuleName() == 'checkout' && $this->getRequest()->getControllerName() != 'cart')
		{
			return false;			
		}
		return true;
	}
}