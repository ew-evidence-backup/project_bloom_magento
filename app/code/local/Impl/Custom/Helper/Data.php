<?php
class Impl_Custom_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    /* Update zipcode index for a give product
     *
     * @param int $productId id on a product
     * @param string $zipcodesStr string containing all zipcodes 
     *
     *return void
     */
    public function updateZipcodeIndex($productId, $zipcodesStr) {
        $zipCodes = explode("\n", $zipcodesStr);
        
        foreach($zipCodes as $zip)
        {
            $zip = trim($zip);
            if(!empty($zip))
            {
                $pz = Mage::getModel('custom/productzip')->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('zip', $zip);
                if(count($pz) == 0)
                {
                    $pz = Mage::getModel('custom/productzip');
                    $pz->setProductId($productId);
                    $pz->setZip($zip);
                    $pz->save();
                }
            }
        }        

    }
}
