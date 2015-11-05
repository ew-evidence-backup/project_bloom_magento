<?php

class Bloomnation_Iframemod_ProductController extends Mage_Core_Controller_Front_Action {
    
    public function viewAction() {
        Mage::getSingleton('checkout/session')->setRedirectUrl('/iframemod/checkout/cart');

        $productId = (int)$this->getRequest()->getParam('id');
        Mage::register('current_product_id', $productId);
        
        // Layout functions
        $this->loadLayout();
        $this->renderLayout();
    }
    
}