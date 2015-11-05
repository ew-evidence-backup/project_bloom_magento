<?php
class Web_QuickCheckout_Model_Observer{
    public function QuickCheckoutRedirect($observer) {
        if (Mage::helper('quickcheckout')->getQuickCheckoutConfig('general/active')) {
	    if(strstr($_SERVER['HTTP_REFERER'],'/iframemod/')) {
		$url = '/iframemod/checkout/onepage';
		header('Location: '.$url);
		exit();
	    } else {
		Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onestep"));
	    }
        }
    }	
}