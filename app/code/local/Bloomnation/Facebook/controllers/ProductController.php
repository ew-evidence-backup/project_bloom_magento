<?php

class Bloomnation_Facebook_ProductController extends Mage_Core_Controller_Front_Action {
    
    public function viewAction() {
        $productId = (int)$this->getRequest()->getParam('id');
        Mage::register('current_product_id', $productId);
        
        // Layout functions
        $this->loadLayout('facebook_index_index');
        $this->renderLayout();
    }
    
}