<?php

class Bloomnation_Iframemod_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function vendorAction() {
        $vendor = $this->getRequest()->getParam('id',false);

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('image','left')
            ->addAttributeToSelect('name','left')
            ->addAttributeToFilter('udropship_vendor', $vendor)
            ->addAttributeToSort('global_position','asc')
            ->addAttributeToSort('price', 'asc');

        $this->loadLayout();
        $this->getLayout()->getBlock('content')->setProductCollection($products);
        $this->renderLayout();
    }
}

?>