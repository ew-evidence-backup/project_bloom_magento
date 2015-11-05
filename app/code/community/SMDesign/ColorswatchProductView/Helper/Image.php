<?php
class SMDesign_Colorswatchproductview_Helper_Image extends Mage_Catalog_Helper_Image {
	
	protected $_product;
	protected $_currentSimpleProduct;
	
	public function getProduct() {
		if (empty($this->_product)) {
			$this->_product = Mage::registry('product');
		}
    return $this->_product;
	}
  
	public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null) {
		
		$ModuleName = Mage::app()->getRequest()->getModuleName();
		$ControllerName = Mage::app()->getRequest()->getControllerName();
		$ActionName = Mage::app()->getRequest()->getActionName();
		$RouteName = Mage::app()->getRequest()->getRouteName();

		$controller = "{$ModuleName}_{$ControllerName}_{$ActionName}";
		
		Mage::getModel('catalog/session')->setCurrentSimpleProduct(null);
		Mage::getModel('catalog/session')->setCurrentSimpleProductId(null);

		if ('catalog_product_view' == $controller && is_object($this->getProduct()) && Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE == $this->getProduct()->getTypeId()) {
			
			$attributes = Mage::helper('core')->decorateArray($this->getProduct()->getTypeInstance(true)->getConfigurableAttributes($this->getProduct()));
			$selectedAttributeCode = false;
			
			foreach ($attributes as $attribute) {
				$selectedOption = Mage::app()->getRequest()->getParam($attribute->getProductAttribute()->getAttributeCode(), false);
				if ($selectedOption) {
					$selectedAttributeCode = $attribute->getProductAttribute()->getAttributeCode();
				}
			}
			
			if ($selectedAttributeCode) {
				
				$allProducts = $this->getProduct()->getTypeInstance(true)->getUsedProducts(null, $this->getProduct());
				foreach ($allProducts as $simpleProduct) {
			    if ($simpleProduct->isSaleable() && $simpleProduct->getData($selectedAttributeCode) == Mage::app()->getRequest()->getParam($selectedAttributeCode)) {
			    	$simpleProduct->load();

			    	if (count($simpleProduct->getMediaGalleryImages()) > 0) {
			        $products[] = $simpleProduct;
			    	}
			    }
				}
			}

			if (isset($products) && is_array($products) && count($products) > 0) {
				$product = $this->_currentSimpleProduct = $products[0];
				Mage::getModel('catalog/session')->setCurrentSimpleProduct($product);
				Mage::getModel('catalog/session')->setCurrentSimpleProductId($product->getId());
				
				if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) {
					$this->getProduct()->setData('media_gallery_images', $product->getMediaGalleryImages());
				}
			}
		}
		
		return parent::init($product, $attributeName, $imageFile);
	}
	
}