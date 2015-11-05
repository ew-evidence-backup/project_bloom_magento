<?php
class Unirgy_Dropship_CouponController extends Unirgy_Dropship_Controller_VendorAbstract
{
    public function indexAction() {
        $this->_renderPage();
    }
    public function chooseAction()
    {
        $this->_renderPage();
    }
    public function newAction()
    {
        $this->_renderPage();

    }
    public function saveAction()
    {
        if($this->getRequest()->isPost())
        {
            Mage::helper('udropship/coupon')->genVendorCoupon($this->getRequest()->getParams());
        }
        $this->_redirect('florist/coupon/index');
    }
    public function editAction()
    {

    }
}