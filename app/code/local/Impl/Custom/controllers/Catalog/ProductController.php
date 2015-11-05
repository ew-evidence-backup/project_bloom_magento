<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


include_once("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Impl_Custom_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
	public function update_zipAction()
    {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query("DELETE FROM impl_product_zip");
		$write->query("ALTER TABLE impl_product_zip AUTO_INCREMENT=1");
		
		$vendorList = Mage::getModel('udropship/vendor')->getCollection()->addFieldToSelect('*');
		$productIds = Mage::getModel('catalog/product')->getCollection()->getAllIds();
		$pz = Mage::getModel('custom/productzip');
		if(count($vendorList) > 0)
		{
			foreach($vendorList as $vendor)
			{
				Mage::helper('udropship')->loadCustomData($vendor);
				$products = array_keys($vendor->getAssociatedProducts());
				$zipCodes = explode("\n", $vendor->getLimitZipcode());
				if(count($products) > 0)
				{
					foreach($products as $product)
					{
						foreach($zipCodes as $zip)
						{
							$zip = trim($zip);
							if(!empty($zip))
							{
								$pz = Mage::getModel('custom/productzip')->getCollection()->addFieldToFilter('product_id', $product)->addFieldToFilter('zip', $zip);
								if(count($pz) == 0)
								{
									$pz = Mage::getModel('custom/productzip');
									$pz->setProductId($product);
									$pz->setZip($zip);
									$pz->save();
								}
							}
						}
					}
				}
			}
		}

		/*$products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		foreach($products as $val)
		{
			$val->save();
		}*/


		Mage::getSingleton('core/session')->addSuccess('Products zip codes successfully updated.');
		$this->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/index"));
	}

public function update_vendor_stockAction(){
	
	// update UDropShip Qty
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	$write->query("UPDATE udropship_vendor_product SET stock_qty = 100000.0000 WHERE 1");
	
	
	//Update Magento Product Qty
	$write->query("UPDATE cataloginventory_stock_item s_i, cataloginventory_stock_status s_s 
	SET s_i.qty = '100000', s_i.is_in_stock = '1',
	s_s.qty = '100000', s_s.stock_status = '1'
	WHERE s_i.product_id = s_s.product_id ");

	// end
	Mage::getSingleton('core/session')->addSuccess('Products quantity successfully updated.');
	$this->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/index"));
	
}

}
