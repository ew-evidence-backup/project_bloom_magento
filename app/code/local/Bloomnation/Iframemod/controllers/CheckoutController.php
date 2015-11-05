<?php

class Bloomnation_Iframemod_CheckoutController extends Mage_Core_Controller_Front_Action {
    
    public function cartAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function onepageAction() {
        $this->loadLayout();
        $this->renderLayout();        
    }
    
}