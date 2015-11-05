<?php

class Bloomnation_Urls_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer) {
        $this->_redirectOldPages();
        
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        // Add this router
        $front->addRouter('bloomnation', $this);
        // Add catalog URL rewrites router too
        $catalogRouter = new Bloomnation_Urls_Controller_CatalogRouter();
        $front->addRouter('bloomnation-catalog', $catalogRouter);
        // The subdomain logic

        //$subdomainRouter = new Bloomnation_Urls_Controller_SubdomainRouter();
        //$front->addRouter('bloomnation-subdomain', $subdomainRouter);

    }
    
    /**
     *  See if any product matches
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request) {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        
        // Clear session to make sure users can add products and the shopping cart clears properly
        if(isset($_GET['zipcode'])) {
            Mage::helper('urls')->clearSession($_GET['zipcode']);
        }
        
        // Get basic data
        $identifier = urldecode(strtolower(trim($request->getPathInfo(), '/')));
        $vendorsCollection = Mage::helper('urls')->getVendors();      
        $vendorId = false;
        $productSlug = '';
        
        // Get vendor id
        $vendorId = $this->_detectVendorId($identifier, $vendorsCollection);
        // Get product slug
        preg_match('/.*\/(.*).html$/', $identifier, $matches);
        $productSlug = $matches[1];

        if($vendorId) {
            Mage::register('vendorId',$vendorId);
            $productId = Mage::helper('urls')->getProductIdBySlug($productSlug, $vendorId);

            if('' == $productId || empty($productId)) {
                return false;
            }
            // Point to correct product
            $request->setModuleName('catalog')
                ->setControllerName('product')
                ->setActionName('view')
                ->setParam('id', $productId);
            return true;
        }
        
        return false;
    }
    
    /* Try to detect vendor id
     *
     * @param: string $url 
     * @param: collection vendorsCollection
     *
     * return int $vendorId || bool false
     */
    
    protected function _detectVendorId($url, $vendorsCollection) {
        $vendorId   = false;
        $vendor      = '';

        // Split url identifier into parts
        preg_match('/(.*)\/.*\.html$/',$url,$matches);
        
        $cityAndFlorist = $matches[1];
        $cityAndFlorist = explode('-',$cityAndFlorist);

        for($index = 1;$index < sizeof($cityAndFlorist); $index++) {
            $city = implode('-',array_slice($cityAndFlorist,0,$index));
            $vendorName = implode('-',array_slice($cityAndFlorist,$index));
            
            // Try to determine city by matching first two letters
            foreach($vendorsCollection as $vencity=>$vendors) {
                if($city == $vencity) {
                    foreach($vendors as $vendor) {
                        if($vendor['vendor_name'] == $vendorName) {
                            // Set current vendor and location info
                            Mage::register('current_vendor',$vendor['vendor_name']);
                            Mage::register('current_location', $vendor['city']);
                            // Return the needed value
                            $vendorId = $vendor['vendor_id'];
                            return $vendorId;
                        }
                    }
                }
            }            
        }

        return $vendorId;
    }
    
    protected function _redirectOldPages() {
        // Check for 'old' style products URL for redirect to new style
        $path = $_SERVER['REQUEST_URI'];
        if(substr_count($path,'/') == 1 && $path != '/') {
            preg_match('/^\/(.*)\.html/',$path,$matches);
            if(isset($matches[1])) {
                $slug = $matches[1];
                if($id = Mage::helper('urls')->getProductIdBySlug($slug)) {
                    if($url = Mage::getModel('catalog/product')->load($id)->getProductUrl()) {
                        header("HTTP/1.1 301 Moved Permanently"); 
                        header("Location: ".$url);
                        exit();
                    }
                }
            }
        }
        
        
    }
}
    
?>