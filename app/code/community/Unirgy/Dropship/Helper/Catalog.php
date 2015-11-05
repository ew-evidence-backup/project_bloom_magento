<?php

class Unirgy_Dropship_Helper_Catalog extends Mage_Core_Helper_Abstract {
    
    /* Create category to be associated with florist
     *
     * no params
     *
     *@return void
     */
    public function createVendorCategory() {
        // The parent category you want to work with.
        $vendor = Mage::getSingleton('udropship/session')->getVendor();
        $parentCategory = Mage::getModel('catalog/category')->load(20); // 'Florists' category 
        
        $categoryName = $vendor->getVendorName();
        $categoryUrlKey = strtolower(preg_replace('/\W/','-',$categoryName));
        
        $category = Mage::getModel( 'catalog/category' )
            ->setStoreId( Mage_Core_Model_App::ADMIN_STORE_ID )
            ->setName( $categoryName ) // The name of the category
            ->setUrlKey( $categoryUrlkey ) // The category's URL identifier
            ->setIsActive( 1 ) //  Is it enabled?
            ->setIsAnchor( 1 ) // I think this relates to whether it shows in navigation.
            ->setDisplayMode( 'PRODUCTS')
            ->setCustomDesignApply(1)
            ->setCustomDesign('pro/bloom_vendor')
            ->setPath( $parentCategory->getPath() ) // Important you get this right.
            ->setYelpApi( $vendor->getId() )
        ->save();
        
        $categoryId = $category->getId();
        
    }
    
    /* Create batch product
     *
     * @param array $data
     *
     * @return bool $success
     */
    public function addBatchProduct($data) {
        // Required keys
        $requiredData = array('imgSrc','productName','productPrice');
        // Get needed APIs instances
        $api = new Mage_Catalog_Model_Product_Api();
        $attribute_api = new Mage_Catalog_Model_Product_Attribute_Set_Api();
        $attribute_sets = $attribute_api->items();
        // Check if data was passed in correctly
        foreach($requiredData as $req) {
            if(!isset($data[$req]) || empty($data[$req])) {
                throw new Exception('Please make sure that all keys of passed data array exist and not empty:'.implode(',',$requiredData));
            }
        }
        
        
        // Product required params
        $sku = strtoupper(preg_replace('/\W/','-',$data['productName']));
        $productData = array(); 
        $productData['website_ids'] = array(1); 
        $productData['status'] = 1;
        $productData['name'] = utf8_encode($data['productName']);
        $productData['description'] = '';
        $productData['short_description'] = '';
        $productData['weight'] = 10.00;
        $productData['tax_class_id'] =2;
        
        $new_product_id = $api->create('simple', $attribute_sets[0]['set_id'], $sku, $productData);

        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct( $new_product_id );
         
        $stockItem->setData('use_config_manage_stock', 1);
        $stockItem->setData('qty', 10000);
        $stockItem->setData('min_qty', 0);
        $stockItem->setData('use_config_min_qty', 1);
        $stockItem->setData('min_sale_qty', 0);
        $stockItem->setData('use_config_max_sale_qty', 1);
        $stockItem->setData('max_sale_qty', 0);
        $stockItem->setData('use_config_max_sale_qty', 1);
        $stockItem->setData('is_qty_decimal', 0);
        $stockItem->setData('backorders', 0);
        $stockItem->setData('notify_stock_qty', 0);
        $stockItem->setData('is_in_stock', 1);
        $stockItem->setData('tax_class_id', 0);
         
        $stockItem->save();
        
        $product = Mage::getModel('catalog/product')->load($new_product_id);
        // Image
        $ext = '.'.pathinfo(parse_url($data['imgSrc'], PHP_URL_PATH), PATHINFO_EXTENSION);
        $filename = md5(mt_rand(0,1111111)).$ext; // Create name
        $imgPath = Mage::getBaseDir().'/media/vendorimageupload/'.$filename;
        file_put_contents($imgPath, file_get_contents($data['imgSrc'])); // Move image from facebook to local server
        $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
        $product->addImageToMediaGallery($imgPath, array ('image','small_image','thumbnail'), true, false); // Add to product gallery
        @unlink($imgPath); // Remove file that was added to magento
        // Product data
        $product->setUdropshipVendor(Mage::getSingleton('udropship/session')->getId());
        $product->setPrice(number_format($data['productPrice'], 2, null, ''));
        
        $cat = Mage::helper('udropship')->getCategoryByVendor(Mage::getSingleton('udropship/session')->getId());
        if($cat) {
            $product->setCategoryIds(array($cat->getId()));
        }
        
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
        $product->save();
    }
    
    /* Update items stock
     *
     */
    public function massUpdateStock($vendorId, $productId) {
	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        
        // update UDropShip Qty
	$write->query("UPDATE udropship_vendor_product SET stock_qty = 100000.0000");
	
	//Update Magento Product Qty
	$write->query("UPDATE cataloginventory_stock_item s_i, cataloginventory_stock_status s_s 
	SET s_i.qty = '100000', s_i.is_in_stock = '1',
	s_s.qty = '100000', s_s.stock_status = '1'
	WHERE s_i.product_id = s_s.product_id");
    }
}
