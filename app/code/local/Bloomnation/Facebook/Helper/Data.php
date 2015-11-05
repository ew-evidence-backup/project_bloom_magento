<?php

class Bloomnation_Facebook_Helper_Data extends Mage_Core_Helper_Abstract{
    
    /* Get vendor ID by facebook id
     * user direct SQL to speed queries up
     *
     * @params int $facebookId -- id of a facebook page
     *
     * @return (int) vendorId or false
     */
    function getVendorByFacebookId($facebookId) {
        // Execute SQL query to get vendor id or return false
        $query = "SELECT vendor_id FROM udropship_vendor 
                        WHERE facebook_page_id='$facebookId'";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        return $result;
    }
}
