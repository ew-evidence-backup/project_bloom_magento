<?php

class Queldorei_Priceslider_SliderController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {
        $categoryId = (int)Mage::registry('current_subd_category')->getId();
        
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        Mage::register('current_category', $category);

        $this->loadLayout();
        $this->renderLayout();
    }
}