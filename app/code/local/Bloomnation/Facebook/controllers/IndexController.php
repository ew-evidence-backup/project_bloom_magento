<?php

class Bloomnation_Facebook_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction() {
        $data = $this->getRequest()->getPost('signed_request',null);
        if(null == $data) {
            if('' != $_COOKIE['fbPageId']) {
                $data = array('yes');
                $fbRequest['page']['id'] = $_COOKIE['fbPageId'];
            }
        } else {
            // Initialize data
            $facebook = new Facebook_Bloomnation();
            // Get facebook request
            $fbRequest = $facebook->getSignedRequest();
        }
        

        // ERROR: Exit if it's not a request from facebook canvas page
        if(null == $data) { exit('This page can only be viewed as facebook canvas page'); }

        // Get the vendor data
        $vendorId = Mage::helper('facebook')->getVendorByFacebookId($fbRequest['page']['id']);
        setcookie('fbPageId', $fbRequest['page']['id']);
 
        // ERROR: no vendor
        if(!$vendorId) { exit('You are almost ready to open your Facebook Store! Please click the link below and you will receive an email with a link to open your store!<br/><a href="/facebookstore/index/report">Click here</a>'); }
        // Load category data
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('yelp_api',$vendorId)
            ->setCurPage(1)->setPageSize(1);
        $_cat = $collection->getFirstItem();
        Mage::register('current_category',$_cat);
        // ERROR category not found for the vendor
        if(sizeof($_cat->getData()) < 1) { exit('Sorry this user doesn\'t seem to have facebook store setup with Bloom Nation or some profile information is missing'); }

        $products = $_cat->getProductCollection()->addAttributeToSort('global_position','asc');
        
        // Layout functions
        $this->loadLayout('facebook_index_index');
        // Assign data to content block
        $this->getLayout()->getBlock('content')->setProductCollection($products);

        $this->renderLayout();
    }
    
    public function editAction() {
        // Layout functions
        $this->loadLayout();
        // If user is logged in display different template
        if(Mage::getSingleton('udropship/session')->isLoggedIn()) {
            $this->getLayout()->getBlock('content')->setTemplate('facebook/setup/logged-in.phtml');
        }
        
        $this->renderLayout();
    }
    
    public function registerAction() {
        // Layout functions
        $this->loadLayout();        
        $this->renderLayout();        
    }
    
    public function reportAction() {
        Mage::helper('facebook/email')->mailreport();
        exit('error sending report, please try again later');
    }
}

?>