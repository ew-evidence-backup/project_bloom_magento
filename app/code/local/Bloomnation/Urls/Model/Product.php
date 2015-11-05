<?php

class Bloomnation_Urls_Model_Product extends Mage_Catalog_Model_Product {
    /**
     * Retrieve Product URL
     *
     * @param  bool $useSid
     * @return string
     */
    public function getProductUrl($useSid = null) {
        $vendor = Mage::helper('urls')->getAssocVendors($this->getId());

        $url = '/'.$vendor['uv.city'].'-'.$vendor['uv.vendor_name'].'/'.$this->getUrlKey().'.html';
 
        return $url;
    }
    
    /* Get facebook URL of this product
     *
     * return string $url
     */
    public function getFacebookProductUrl() {
        return Mage::getUrl('facebook/product/view', array('_forced_secure'=>true)).'id/'.$this->getId();
    }
    
    /* Get iframe URL of this product
     *
     * return string $url
     */
    public function getIframeProductUrl() {
        return Mage::getUrl('iframemod/product/viewr').'id/'.$this->getId();
    }    
    
    public function storeVendorData($vendorId)
    {
        $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                ->addProductFilter($this->getId())
                ->addVendorFilter($vendorId);
                
        if(count($collection) < 1)
        {
                $pv = Mage::getModel('udropship/vendor_product');
                $pv->setVendorId($vendorId);
                $pv->setProductId($this->getId());
                $pv->setPriority(9999);
                $pv->save();
        }
        
        $_associatedProducts = array();
        if ($this->getTypeId() == 'configurable')
        {
                $_associatedProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $this);
        }
        if(count($_associatedProducts) > 0)
        {
                foreach($_associatedProducts as $p)
                {
                        if($p->getUdropshipVendor() != $vendorId)
                        {
                                $p->save();
                        }
                }
        }
    }
    
    /**
     * Check product options and type options and save them, too
     */
    protected function _beforeSave()
    {
        $this->cleanCache();
        $this->setTypeHasOptions(false);
        $this->setTypeHasRequiredOptions(false);

        $this->getTypeInstance(true)->beforeSave($this);

        $hasOptions         = false;
        $hasRequiredOptions = false;

        /**
         * $this->_canAffectOptions - set by type instance only
         * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
         * or in type instance as well
         */
        $this->canAffectOptions($this->_canAffectOptions && $this->getCanSaveCustomOptions());
        if ($this->getCanSaveCustomOptions()) {
            $options = $this->getProductOptions();
            if (is_array($options)) {
                $this->setIsCustomOptionChanged(true);
                foreach ($this->getProductOptions() as $option) {
                    $this->getOptionInstance()->addOption($option);
                    if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
                        $hasOptions = true;
                    }
                }
                foreach ($this->getOptionInstance()->getOptions() as $option) {
                    if ($option['is_require'] == '1') {
                        $hasRequiredOptions = true;
                        break;
                    }
                }
            }
        }

        /**
         * Set true, if any
         * Set false, ONLY if options have been affected by Options tab and Type instance tab
         */
        if ($hasOptions || (bool)$this->getTypeHasOptions()) {
            $this->setHasOptions(true);
            if ($hasRequiredOptions || (bool)$this->getTypeHasRequiredOptions()) {
                $this->setRequiredOptions(true);
            } elseif ($this->canAffectOptions()) {
                $this->setRequiredOptions(false);
            }
        } elseif ($this->canAffectOptions()) {
            $this->setHasOptions(false);
            $this->setRequiredOptions(false);
        }

        if($this->getMinimalVendorPrice() == '')
        {
            $price = (double)$this->getPrice();
            $this->setMinimalVendorPrice($price);
        }
        parent::_beforeSave();
    }
    
    /**
     * Saving product type related data
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _afterSave()
    {
        $this->getLinkInstance()->saveProductRelations($this);
        $this->getTypeInstance(true)->save($this);

        /**
         * Product Options
         */
        $this->getOptionInstance()->setProduct($this)
            ->saveOptions();

        /**
         * Udropship vendor after save product
         */		
        $product = Mage::getModel('catalog/product')->load($this->getId());
        $defaultVendorId = Mage::helper('udropship')->getLocalVendorId($product->getStoreId());

        if($defaultVendorId != $product->getUdropshipVendor())
        {
                $product->storeVendorData($product->getUdropshipVendor());
        }
	
        
        $a = parent::_afterSave();
        $product->storeMinimalPriceByVendors();
        return $a;
    }

    public function getProductVendors()
    {
        // Little trick to pull 'cookie' from magento registry, so permalinks are possible to work
        if(isset($_COOKIE["bloom_nations_zipcode"])) {
            $_COOKIE["bloom_nations_zipcode"] = (Mage::registry('bloom_nations_zipcode')) ?: $_COOKIE["bloom_nations_zipcode"];
            $_COOKIE["bloom_nations_zipcode"] = addslashes((string)$_COOKIE["bloom_nations_zipcode"]);
        }
        if(isset($_COOKIE["bloom_nations_zipcode"]) && !empty($_COOKIE["bloom_nations_zipcode"]) && $_COOKIE["bloom_nations_zipcode"] != '00000')
        {
            $collection = Mage::getModel('custom/productzip')->getCollection()
                ->addFieldToFilter('product_id', $this->getId())
                ->addFieldToFilter('zip', $_COOKIE["bloom_nations_zipcode"]);
                    
            if(count($collection) < 1)
            {
                return array(Mage::getModel('udropship/vendor')->load(1));
            }
            $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                ->addProductFilter($this->getId());
            $output = array();
            if(count($collection) > 0)
            {
                foreach($collection as $vendor)
                {
                    $vendor = Mage::getModel('udropship/vendor')->load($vendor->getVendorId());
                    $zipCodes = explode("\n", $vendor->getData('limit_zipcode'));
                    foreach($zipCodes as &$val)
                    {
                        $val = trim($val);
                    }
                    if(in_array((string)$_COOKIE["bloom_nations_zipcode"], $zipCodes))
                    {
                        $output[] = $vendor;
                    }
                }
                return $output;
            }
        }
            
        if($_COOKIE["bloom_nations_zipcode"] == '00000')
        {
            return array(Mage::getModel('udropship/vendor')->load(1));
        }
        
        return array();
    }
    
    public function getRandomVendor()
    {
            $vendors = $this->getProductVendors();
            if(count($vendors) > 0)
            {
                    $rnd = rand(0, (count($vendors) - 1));
                    return $vendors[$rnd];
            }
            return Mage::getModel('udropship/vendor')->load($this->getUdropshipVendor());
    }
    
    public function getVendorPrice($vendor)
    {
            $product = Mage::getModel('catalog/product')->load($this->getId());
            $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                    ->addProductFilter($this->getId())
                    ->addVendorFilter($vendor->getId());
            if(count($collection) < 1)
            {
                    return $product->getFinalPrice();
            }
            $vendor = $collection->getFirstItem();
            if($vendor->getVendorCost() > 0)
            {
                    return $vendor->getVendorCost();
            }
            else
            {
                    return $product->getFinalPrice();
            }
    }
    
    public function isInVendorStock($vendor)
    {
            if(!is_object($vendor))
            {
                    $vendorId = $vendor;
            }
            else
            {
                    $vendorId = $vendor->getId();
            }
            
            if($this->getTypeId() == 'configurable')
            {
                    $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $this);
                    if(count($childProducts) > 0)
                    {
                            foreach($childProducts as $p)
                            {
                                    $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                                            ->addProductFilter($p->getId())
                                            ->addVendorFilter($vendorId);
                                    if($collection->getFirstItem()->getStockQty() > 0)
                                    {
                                            return true;
                                    }
                            }
                            return false;
                    }
            }
            else
            {
                    $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                            ->addProductFilter($this->getId())
                            ->addVendorFilter($vendorId);
                    if($collection->getFirstItem()->getStockQty() > 0)
                    {
                            return true;
                    }
                    else
                    {
                            return false;
                    }
            }
    }
    
    public function sortmulti ($array, $index, $order, $natsort=FALSE, $case_sensitive=FALSE) {
        if(is_array($array) && count($array)>0) {
            foreach(array_keys($array) as $key)
            $temp[$key]=$array[$key][$index];
            if(!$natsort) {
                if ($order=='asc')
                    asort($temp);
                else   
                    arsort($temp);
            }
            else
            {
                if ($case_sensitive===true)
                    natsort($temp);
                else
                    natcasesort($temp);
            if($order!='asc')
                $temp=array_reverse($temp,TRUE);
            }
            foreach(array_keys($temp) as $key)
                if (is_numeric($key))
                    $sorted[]=$array[$key];
                else   
                    $sorted[$key]=$array[$key];
            return $sorted;
        }
        return $sorted;
    }
	
	
    public function getMinimalPriceByVendors()
    {
            $priceList = array();

            $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                            ->addProductFilter($this->getId());

            if(count($collection) > 0)
            {
                    foreach($collection as $vendor)
                    {
                            $priceList[] = $this->getVendorPrice(Mage::getModel('udropship/vendor')->load($vendor->getVendorId()));
                    }
            }
            asort($priceList);
            foreach($priceList as $val)
            {
                    return $val;
                    break;
            }
            
    }
    
    public function getMinimalPriceByVendorsAndZip()
    {
            $priceList = array();

            $collection = $this->getProductVendors();

            if(count($collection) > 0)
            {
                    foreach($collection as $vendor)
                    {
                            $priceList[] = $this->getVendorPrice($vendor);
                    }
            }
            asort($priceList);
            foreach($priceList as $val)
            {
                    break;
            }
            return $val;
    }	
    
    public function storeMinimalPriceByVendors()
    {
            $price = $this->getMinimalPriceByVendors();
            $price = (double)$price;

            if($this->getMinimalVendorPrice() != $price)
            {
                    $this->setMinimalVendorPrice($price);	
                    $this->save();
            }
    }
    
    /* Assign attribute categories
     * assign category ids that mirror attributes
     *
     * @param int $vendorCat vendor's category id - first part of product categories
     *
     * return void
     */
    public function assignAttrCategories($vendorCat) {
        $catsToAssign = array($vendorCat);
        
        // All occasion related manipulations
        $occasionMatches = $this->getMatchAttrAndCatIds('occasions', 8);
        $flowerMatches = $this->getMatchAttrAndCatIds('flower_type', 22);
        $styleMatches = $this->getMatchAttrAndCatIds('style', 23);
        
        $resCats = implode(',',array_merge($catsToAssign, $flowerMatches, $occasionMatches, $styleMatches));
        // Add parent categories if needed
        if(!empty($occasionMatches)) {
            $resCats.= ',8';
        }
        if(!empty($flowerMatches)) {
            $resCats.= ',22';
        }
        if(!empty($styleMatches)) {
            $resCats.= ',23';
        }

        $this->setCategoryIds($resCats);
    }
    
    
    /* Get matching values between category ids and attr code of a product
     *
     * @param string $attrCode code of an eav attribute
     * @param int $catId id of a category
     *
     * return array $matchesCats;
     */
    protected function getMatchAttrAndCatIds($attrCode, $catId) {
        $matchCats = array();
        $occasionOptions = Mage::getSingleton('eav/config')
                                        ->getAttribute('catalog_product', $attrCode)
                                        ->getSource()
                                        ->getAllOptions(false);
        $prodOccasions = explode(',',$this->getData($attrCode));
        $occasionCatOptions = Mage::getModel('catalog/category')->load($catId)->getChildren();
        // 35 for 'name'
        $query = "SELECT value as label, entity_id
                            FROM catalog_category_entity_varchar
                            WHERE attribute_id=35
                            AND entity_id IN($occasionCatOptions)";
        $catNames = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);

        foreach($occasionOptions as $oO) {
            foreach($prodOccasions as $pO) {
                if($oO['value'] == $pO) {
                    $prodSelOcc[] = $oO;
                }
            }
        }
        foreach($prodSelOcc as $sO) {
            foreach($catNames as $cN) {
                if(strtolower($sO['label']) == strtolower($cN['label'])) {
                    $matchCats[] = $cN['entity_id'];
                }
            }
        }
        
        return $matchCats;
    }
}
