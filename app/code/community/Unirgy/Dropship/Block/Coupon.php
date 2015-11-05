<?php
class Unirgy_Dropship_Block_Coupon extends Mage_Core_Block_Template
{
    public function checkSku()
    {
        $skus = $this->getRequest()->getParam('skus');
        if( !is_array($skus) || !count($skus))
        {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('*/*/choose'));
            return;
        }
        $this->setSkus($skus);
    }
    public function getVendorCoupons()
    {
        $vid = Mage::getSingleton('udropship/session')->getVendor()->getVendorId();
        $vc = Mage::getModel('udropship/vendor_coupon')->getCollection()->addFieldToFilter('vendor_id',array('eq'=>$vid));
        return $vc;

    }
}