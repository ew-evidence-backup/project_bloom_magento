<?php

class Bloomnation_Urls_Helper_Data extends Mage_Core_Helper_Abstract{

    public function getImagePath($product)
    {
        $imageUrl = Mage::getModel('catalog/product_media_config')
            ->getMediaUrl( $product->getImage() );

        $baseDir =  Mage::getBaseDir() ;

        $withoutIndex = str_replace('index.php/','', Mage::getBaseUrl());

        $imageWithoutBase = str_replace($withoutIndex,'' , $imageUrl);
        $imagePath = $baseDir.DIRECTORY_SEPARATOR.$imageWithoutBase ;
        return array('path'=>$imagePath,'url'=>$imageUrl);
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }
    /**
     * Get array of cities from vendor collection
     *
     * @return array $collection
     */
    function getVendors() {
        $collection = Mage::getModel('udropship/vendor')->getCollection()
            ->addFieldToSelect('city')
            ->addFieldToSelect('vendor_id')
            ->addFieldToSelect('vendor_name')
            ->getData();
        
        foreach($collection as $i=>$florist) {
            $florist = str_replace(' ','-',$florist);
            $florist = str_replace('\'','',$florist);
            $florist = str_replace('.','',$florist);
            foreach($florist as $k=>$v) { $florist[$k] = strtolower($v); }
            $collection[$i] = $florist;
        }
        
        foreach($collection as $i=>$fl) {
            $resultArr[$fl['city']][] = $fl;
        }

        return $resultArr;
    }
    function getVendorsUrl($url ='') {
        if($url==''){return array();}
        $collection = Mage::getModel('udropship/vendor')->getCollection()
            ->addFieldToSelect('city')
            ->addFieldToSelect('vendor_id')
            ->addFieldToSelect('vendor_name')
            ->getData();

        foreach($collection as $i=>$florist) {
            $florist = str_replace(' ','-',$florist);
            $florist = str_replace('\'','',$florist);
            $florist = str_replace('.','',$florist);
            if(strtolower($florist['vendor_name']) == $url){
                return array($florist);
            }
        }
        return array();
    }
    /**
     * Get product ID by its url slug
     * use raw SQL query to speed things up
     * 
     * @param string $slug
     * @param int $id
     * 
     * return int $id || bool false
     */
    function getProductIdBySlug($slug,$vendorId = 'nocheck') {
        $slug = addslashes((string) $slug); // Make sure slug is a string and its sanitized
        
        $query = "SELECT entity_id FROM catalog_product_entity_varchar WHERE value='$slug'";
        
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);
        foreach($result as $_p) {
            $checkData = $this->getAssocVendors($_p);
            if($checkData['vendor_id'] == $vendorId || 'nocheck' == $vendorId) {
                return $_p;
            }
        }
        
        return false;
    }
    
    /**
     * Get vendor associated with product
     * 
     * @params int $productId
     *
     * @return array $vendorInfo
     */
    public function getAssocVendors($productId) {
        $assocVendor = Mage::getModel('udropship/vendor_product')->getCollection()
            ->addProductFilter($productId);
        $assocVendor->getSelect()
                    ->join(
                            array('uv' => $assocVendor->getTable('udropship/vendor')),
                            'uv.vendor_id=main_table.vendor_id',
                            array(
                                 'vendor_name AS uv.vendor_name',
                                 'city AS uv.city'
                                )
                          );
        $data = $assocVendor->getData();
        $data = $data[0];
        foreach($data as $k=>$v) {
            $data[$k] = strtolower($data[$k]);
            $data[$k] = str_replace(' ','-',$data[$k]);
            $data[$k] = str_replace('\'','',$data[$k]);
            $data[$k] = str_replace('.','',$data[$k]);
        }

        return $data;
    }
    
    /**
     * Clear session
     *
     * @param (string) $zipcode -- check cookies against this zipcode
     * 
     * return void
     */
    public function clearSession($zipcode) {
        if(isset($_COOKIE['bloom_nations_zipcode'])) {
            if($zipcode != $_COOKIE['bloom_nations_zipcode']) {
                Mage::getSingleton('checkout/session')->clear();
            }
        }
    }
    
    /* Get category name || title override
     *
     * no params
     *
     * return string $name || bool false
     */
    public function getTitleOverride() {
        $override = Mage::registry('bloom_nations_zipcode_info');
        $_category = Mage::registry('current_category');
        
        if($_category && $override) {
            if(!$_SESSION['nolocal']) {
                if($_category->getId() == 8) {
                    $override = 'Flower Delivery by Florists near '.$override;
                } else {
                    $override= $_category->getName().' Bouquets near '.$override;
                }
            } else {
                $override = 'No local florists near '.$override;
            }
            // Override for Roses to Rose
            $override = preg_replace('/^Roses/','Rose',$override);
        }
        // Override for Store Fronts
        if($_category && $_category->getParentId() == 20) {
            $vendor_id    = $_category->getYelp_api();
            $dropship_vendor = Mage::getModel('udropship/vendor')->load($vendor_id);
            $state = Mage::getModel('directory/region')->load($dropship_vendor->getRegionId());
            $override = $dropship_vendor->getVendorName().' - '.$dropship_vendor->getCity().' - '.$state->getCode().', '.$dropship_vendor->getZip();
        }
        // Overrides for subdomains
        if(Mage::registry('current_svendor')) {
            $vendor = Mage::getModel('udropship/vendor')->load(Mage::registry('current_svendor'));
            $state = Mage::getModel('directory/region')->load($vendor->getRegionId())->getCode();
            $path = Mage::app()->getRequest()->getOriginalPathInfo();
            if('/' == $path) {
                $override = $vendor->getCity().'&#039;s Premier Local Florist - '.$vendor->getVendorName();
            } else if ('/occasion.html' == $path) {
                $override = 'Flower Delivery near '.$vendor->getCity().', '.$state;
            }

            $filters = Mage::getSingleton('Mage_Catalog_Block_Layer_State')->getActiveFilters();
            foreach(Mage::getSingleton('Mage_Catalog_Block_Layer_State')->getActiveFilters() as $f) {
                $activeFilters[strtolower($f->getName())] = $f->getLabel();
            }
            if(sizeof($filters) > 0) {
                $override = $activeFilters['style'].
                ' '.$activeFilters['occasions'].
                ' '.$activeFilters['color'].
                ' '.$activeFilters['flower type'].
                ' flowers near '.
                $vendor->getCity().', '.$state;
                $override = trim($override);
            }

        }        
        // Override for product detail
        if('product' == Mage::app()->getRequest()->getControllerName()) {
            $product = Mage::registry('current_product');
            $florist = Mage::registry('current_vendor');
            $location = Mage::registry('current_location');
            if($product && $florist && $location) {
                $override = $product->getName().' near '.ucwords(str_replace('-',' ',$location)).' - '.ucwords(str_replace('-', ' ',$florist));
            } else {
                $override = false;
            }
        }
        
        return $override;
    }
    
    /* Get Meta Description Override
     *
     * no params
     *
     * @return string $overrideText
     */
    public function getMetaOverride() {
        $override = Mage::registry('bloom_nations_zipcode_info');
        $_category = Mage::registry('current_category');
        $overrideText = '';
        // Regular cats
        if($_category && $override) {
            // Occasion override
            if ($_category->getParentId() == 8) {
                $overrideText = 'Send the perfect '.$_category->getName().' flowers from any florist near '.$override.'. Compare Florists, Designs, Reviews and Prices all near one page.';
            } else if ($_category->getParentId() == 22) {
                $overrideText = 'Send a bouquet with '.$_category->getName().' from any florist near '.$override.'. Compare Florists, Designs, Reviews and Prices all near one page.';
            } else if ($_category->getId() == 8) {
                $overrideText = 'Send Unique Floral Bouquets from any Local Florists near '.$override.'. Compare Florists, Designs, Reviews and Prices all near one page.';
            }
        // Store fronts
        } else if ($_category && $_category->getParentId() == 20) {
            $vendor_id    = $_category->getYelp_api();
            $dropship_vendor = Mage::getModel('udropship/vendor')->load($vendor_id);
            $state = Mage::getModel('directory/region')->load($dropship_vendor->getRegionId());            
            $overrideText = 'Premier '.$dropship_vendor->getCity().' florist. '.$_category->getName().' offers fresh flower delivery in and around '.$dropship_vendor->getCity().'. Save money by sending flowers directly with a Local Florist.';
        // Other overrides
        } else {
            $overrideText = 'Send a Unique Flower Arrangement Hand Crafted and Delivered by a Top Local Florists. Compare Florists, Designs &amp; Prices in our Marketplace.';
        }
        
        return $overrideText;
    }
    
    /* Get city by zipcode
     *
     * @param string $zipcode
     *
     * return string $city
     */
    public function getCityByZip($zipcode) {
        // Get zipcode information from the database
	$query = "SELECT primary_city FROM zip_codes_database WHERE  zip = $zipcode";
	$result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        
        return $result;
    }
}