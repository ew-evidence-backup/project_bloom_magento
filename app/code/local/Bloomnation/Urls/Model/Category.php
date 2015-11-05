<?php

class Bloomnation_Urls_Model_Category extends Mage_Catalog_Model_Category {
    
    /**
     * Override return URL fucntion to support city\zipcode oriented URLs
     *
     * 
     * @return string $url
     */
    public function getUrl() {
        // Init variables
        $regUrlKey = Mage::registry('bloom_nations_zipcode_info');
        $primaryCity = false;
        $zipcode = false;
        $url = false;

        if(empty($regUrlKey)) {
            // Try to get current location info
            if(isset($_COOKIE['bloom_nations_zipcode_info'])) {
                $regUrlKey = $_COOKIE['bloom_nations_zipcode_info'];
            } else {
                // Retreat to basic get URL function if initialization failed
                return parent::getUrl();
            }
        }
        
        // Check if this is one of the Store Fronts
        if($this->getParentCategory() && $this->getParentCategory()->getId() == 20) {
            $cat = $this;
            if(!$this->hasData('yelp_api')) {
                $cat = Mage::getModel('catalog/category')->load($this->getId());
            }
            if(($vendor = Mage::getModel('udropship/vendor')->load($cat->getYelpApi()))) {
                $city = str_replace(' ','-',strtolower($vendor->getCity()));
                $url = '/'.$city.'-'.$cat->getUrlKey().'.html';
                return $url;
            }
        }
        
        // Regular Category URL
        if(!empty($regUrlKey)) {
            $regUrlKey = strtolower(str_replace(' ','-',str_replace(',','',$regUrlKey)));
            // Zipcode info in the location
            if(isset($_COOKIE['bloom_nations_zipcode']) && strlen($_COOKIE['bloom_nations_zipcode']) == 5) {
                $zipcode = addslashes((string)$_COOKIE['bloom_nations_zipcode']);
            }
            if(Mage::registry('current_category')) {
                $zipcode = '';
            }            
            if(isset($_GET['zipcode'])) {
                $zipcode = addslashes((string)$_GET['zipcode']);
            }
            // Primary city logic
            if(Mage::registry('primary-city')) {
                $regUrlKey = Mage::registry('primary-city').'-ca/'.substr_replace($regUrlKey,'',-3);
            } elseif(isset($_COOKIE['primary-city']) && !empty($_COOKIE['primary-city'])) {
                $regUrlKey = $_COOKIE['primary-city'].'-ca/'.substr_replace($regUrlKey,'',-3);
            }
            
            $url = '/'.$regUrlKey.'/'.$this->getUrlKey().'-bouquets.html';
            if(strlen($zipcode) == 5) {
                $url .= '?zipcode='.$zipcode;
            }
            return $url;
        }
        
        
        return parent::getUrl();
    }
    
}