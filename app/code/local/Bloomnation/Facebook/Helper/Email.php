<?php
class Bloomnation_Facebook_Helper_Email extends Mage_Core_Helper_Abstract {

    function mailreport() {
        $vsess = Mage::getSingleton('udropship/session');
        
        if($vsess->isLoggedIn()) {
            $vendor = $vsess->getVendor();
            $to = 'ev@bloomnation.com';
            $subject  = 'Facebook page setup error';            
            $message = 'Vendor: '.$vendor->getVendorName()."\r\n";
            $message.= 'Email: '.$vendor->getEmail()."\r\n";
            $message.= 'Facebook page: '.$vendor->getFacebookPage();
            $headers = 'From: '.$vendor->getEmail() . "\r\n" .
                'Reply-To: '.$vendor->getEmail() . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            @mail($to, $subject, $message, $headers);
            exit('Thank you for submitting your request. We\'ll review the store data and get back to you as soon as possible.');
        } else {
            exit('Oopsie Dasies! Looks like something went wrong. <a href="https://apps.facebook.com/343690602390212/" target="_blank">Click HERE</a> to open your Facebook Store.');
        }

        
    }
    
}    
?>