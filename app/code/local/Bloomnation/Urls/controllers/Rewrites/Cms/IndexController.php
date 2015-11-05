<?php

require_once('app/code/core/Mage/Cms/controllers/IndexController.php');

class Bloomnation_Urls_Rewrites_Cms_IndexController extends Mage_Cms_IndexController {

/*
    public function indexAction($coreRoute = null) {
        // Layout functions
        $this->loadLayout('cms_index_index');
        // Assign data to content block
        //$this->getLayout()->getBlock('content')->setProductCollection($products);
        $this->renderLayout();
    }
*/
    public function indexAction($coreRoute = null) {
        // Layout functions
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/home-page.phtml');
        $this->renderLayout();
        // Assign data to content block
        //$this->getLayout()->getBlock('content')->setProductCollection($products);
        //$this->renderLayout();        
    }

    public function aboutusAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function termsofserviceAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function substitutionpolicyAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function privacypolicyAction() {
        $this->loadLayout();
        $this->renderLayout();
    }    
}

?>