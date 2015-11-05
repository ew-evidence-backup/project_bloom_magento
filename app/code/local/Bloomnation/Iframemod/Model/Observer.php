<?php

class Bloomnation_Iframemod_Model_Observer {
    
    public function addToCartComplete($observer) {
        $event = $observer->getEvent();
        if(strstr($_SERVER['HTTP_REFERER'],'/iframemod/')) {
            $url = '/iframemod/checkout/cart';
            
            header('Location: '.$url);
            exit();
        }
    }
    
}