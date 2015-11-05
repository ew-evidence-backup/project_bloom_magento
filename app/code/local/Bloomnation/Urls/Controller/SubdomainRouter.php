<?php

class Bloomnation_Urls_Controller_SubdomainRouter extends Mage_Core_Controller_Varien_Router_Abstract {
    
    public function initControllerRouters($observer) {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();
        // Add this router
        $front->addRouter('bloomnation-subdomain', $this);
    }
    
    public function match(Zend_Controller_Request_Http $request) {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $baseHost = parse_url(Mage::getBaseUrl(),PHP_URL_HOST);
        $curHost = $_SERVER['HTTP_HOST'];
        if($curHost != $baseHost) {            
            // Get the florist id
            $floristId = explode('.',$curHost);
            $floristId = $floristId[0];
            $vendor = Mage::helper('udropship')->getVendorBySubdomain($floristId);

            // Check if vendor exists with this subdomain
            if(($cat = Mage::helper('udropship')->getCategoryByVendor($vendor))) {
                // Register necessary values
                if(!Mage::registry('current_subdomain')) {
                    Mage::register('current_subdomain', $floristId);
                }
                if(!Mage::registry('current_svendor')) {
                    Mage::register('current_svendor', $vendor);
                }
                if(!Mage::registry('subdomain')) {
                    Mage::register('subdomain',true);
                }
                if(!Mage::registry('current_subd_category')) {
                    Mage::register('current_subd_category', $cat);
                }
            } else {
                header("Location: http://".$baseHost); exit();
            }
        }

        return false;
    }
    
}
        
?>