<?php
class Impl_Custom_Block_Crossvendor extends Mage_Catalog_Block_Product_Abstract
{
	protected $_vendors;
	
	public function __construct()
	{
		$this->_vendors = $this->getVendors();
		$this->setTemplate('impl_custom/crossvendor.phtml');
	}
	
	public function getVendors()
	{
		$cart = Mage::getSingleton('checkout/cart');
		$quote = $cart->getQuote();
		if($quote)
		{
			$items = $quote->getAllItems();
			$output = array();
			if(count($items) > 0)
			{
				foreach($items as $item)
				{
					if(!in_array($item->getUdropshipVendor(), $output))
					{
						$output[] = $item->getUdropshipVendor();						
					}
				}
				return $output;
			}
		}
		return array();
	}
	
	public function getProducts()
	{
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');

		$readresult = $write->query("
			SELECT * FROM udropship_vendor_product uvp WHERE uvp.vendor_id IN (" . implode(',',$this->_vendors) . ") AND uvp.is_crossell = 1		
		");
		
		$productIds = array();
		while($row = $readresult->fetch())
		{
			$productIds[] = $row['product_id'];
		}
		
		if(count($productIds))
		{
			$collection = Mage::getModel('catalog/product')->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('entity_id', array('in' => $productIds));
			$collection->getSelect()->order(new Zend_Db_Expr('RAND()'))->limit(5);
			return $collection;
		}
		return array();
	}
}