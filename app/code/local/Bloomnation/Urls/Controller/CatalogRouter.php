<?php

class Bloomnation_Urls_Controller_CatalogRouter extends Mage_Core_Controller_Varien_Router_Abstract {
    
    public function match(Zend_Controller_Request_Http $request) {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        
        // Get basic data
        $identifier = urldecode(strtolower(trim($request->getPathInfo(), '/')));
        $zipArr = array();
        $binfo = '';
        
        //////////////////// /[city]-[state]-flower-delivery.html type of URLS 
        preg_match('/(.*)-flower-delivery.html/',$identifier,$matches);
        if(sizeof($matches) == 0) {
            preg_match('/los-angeles-ca\/(.*)\/flower-delivery.html/',$identifier,$matches);
        }
        
        if(sizeof($matches) > 0) {
            if(strstr($identifier,'los-angeles-ca/')) {
                $matches[1].= '-ca';
                setcookie('primary-city','los-angeles');
                Mage::register('primary-city','los-angeles');
            }

            // Get string representations of zipcode's address
            $binfo.= addslashes(ucwords(str_replace('-',' ',$matches[1])));
            $binfo = substr_replace($binfo,',',-3,0);
            $binfo = substr_replace($binfo,strtoupper(substr($binfo,-3)),-3);

            setcookie("bloom_nations_zipcode_info", $binfo, 0, '/');
            Mage::register('bloom_nations_zipcode_info',$binfo);
            
            // If zipcode parameter is passed in
            if(isset($_GET['zipcode'])) {
                $zipcode = addslashes((string)$_GET['zipcode']);
                // Turn user around if zip code too short
                if(strlen($zipcode < 5)) {
                    return false;
                }

                // Set up the cookie values
                setcookie("bloom_nations_zipcode", $zipcode, 0, '/');
                // Set up registry values for magento
                Mage::register('bloom_nations_zipcode',$zipcode);
                    
                // Point to correct category
                $request->setModuleName('catalog')
                    ->setControllerName('category')
                    ->setActionName('view')
                    ->setParam('id', 8);
                
                return true;
            } else { // If it's generic area request
                $state = addslashes(strtoupper(substr($binfo,-2)));
                $city = addslashes(substr_replace($binfo,"",-4));
                
                // Compare against city name only
                $zipArr = Mage::getModel('urls/cities')->getCityZips($city, $state);

                // If zipcodes found in this city show the category page with products searched
                if(sizeof($zipArr) > 0) {
                    // Register needed value
                    $cookieVal = implode(',',$zipArr);
                    setcookie("bloom_nations_zipcode", $cookieVal, 0, '/');
                    Mage::register('bloom_nations_zipcode',$cookieVal);
                    // Point to correct category
                    $request->setModuleName('catalog')
                        ->setControllerName('category')
                        ->setActionName('view')
                        ->setParam('id', 8);
                    return true;
                }
            }    
        }
        
        //////////////////// [city]-[state]/[category]-bouquets.html type of URLS
        preg_match('/(.*)-(\w+)\/(\S+)-bouquets.html/',$identifier,$matches);
        
        if(sizeof($matches) > 0) {        
            $city = addslashes(ucwords(strtolower(str_replace('-',' ',$matches[1]))));
            $state = addslashes(strtoupper($matches[2]));
            $catNameCheck = $matches[3];
            
            $binfo = $city.', '.$state;
            setcookie("bloom_nations_zipcode_info", $binfo, 0, '/');
            Mage::register('bloom_nations_zipcode_info',$binfo);
            
            // If zipcode parameter is passed in
            if(isset($_GET['zipcode'])) {
                $zipcode = addslashes((string)$_GET['zipcode']);
                $zipArr = array($zipcode);
            } else {      
                // Compare against city name only
                $zipArr = Mage::getModel('urls/cities')->getCityZips($city, $state);
            }
            
            $listCatsIds = array(8,22,23,20);
            foreach($listCatsIds as $id) {
                $list = Mage::getModel('catalog/category')->load($id)->getChildrenCategories();

                foreach($list as $cat) {
                    $catname = str_replace(' ','-',strtolower($cat->getUrlKey()));
                    if($catname == $catNameCheck) {
                        // Register needed value
                        $cookieVal = implode(',',$zipArr);
    
                        setcookie("bloom_nations_zipcode", $cookieVal, 0, '/');
                        Mage::register('bloom_nations_zipcode',$cookieVal);                    
                        
                        // Point to correct category
                        $request->setModuleName('catalog')
                            ->setControllerName('category')
                            ->setActionName('view')
                            ->setParam('id', $cat->getId());
                        return true;                    
                    }
                }
                
                $parentCat = Mage::getModel('catalog/category')->load($id);
                $catname = str_replace(' ','-',strtolower($parentCat->getUrlKey()));
                if($catname == $catNameCheck) {
                   // Register needed value
                   $cookieVal = implode(',',$zipArr);

                   setcookie("bloom_nations_zipcode", $cookieVal, 0, '/');
                   Mage::register('bloom_nations_zipcode',$cookieVal);                    
                   
                   // Point to correct category
                   $request->setModuleName('catalog')
                       ->setControllerName('category')
                       ->setActionName('view')
                       ->setParam('id', $cat->getId());
                   return true;                    
               }               
                
            }
        }

        //////////////////// [city]-[storefront name].html type of URLS
        preg_match('/(.*).html/U',$identifier,$matches);
        $cityAndFlorist = $matches[1];
        $cityAndFlorist = explode('-',$cityAndFlorist);
        
        for($index = 1;$index < sizeof($cityAndFlorist); $index++) {
            $city  = Mage::helper('urls')->slugify(urldecode(implode('-',array_slice($cityAndFlorist,0,$index))));
            $catName = implode('-',array_slice($cityAndFlorist,$index));
            $data = Mage::getModel('catalog/category')->loadByAttribute('url_key',$catName);
            if(!is_object($data) || !($data)) {
                $catName = $matches[3];
                $data = Mage::getModel('catalog/category')->loadByAttribute('url_key',$catName);
            }
            // Check if vendor exists and if vendor city matches with city from URL
            if(is_object($data)) {
                if('' != $data->getYelpApi()) {
                    if(($vendor = Mage::getModel('udropship/vendor')->load($data->getYelpApi()))) {
                        $vCity = Mage::helper('urls')->slugify(strtolower($vendor->getCity()));
                        if($city == $vCity) {
                            // Point to correct category
                            $request->setModuleName('catalog')
                                ->setControllerName('category')
                                ->setActionName('view')
                                ->setParam('id', $data->getId());
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
    
}