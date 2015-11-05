<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Biebersdorf
 * @package    Biebersdorf_CustomerOrderComment
 * @copyright  Copyright (c) 2009 Ottmar Biebersdorf (http://www.obiebersdorf.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Impl_Shipwithdate_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * This method is called by an event when the Customer
     * places an Order in the Onepage-Checkout.
     */
    public function hookToControllerActionPreDispatch($observer)
    {
        //we compare action name to see if that's action for which we want to add our own event
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
            //We are dispatching our own event before action ADD is run and sending parameters we need
            Mage::dispatchEvent("add_to_cart_before", array('request' => $observer->getControllerAction()->getRequest()));
        }
    }

    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }
    public function getResponse()
    {
        return Mage::app()->getResponse();
    }


    public function setProductInfo($observer)
    {
        if ('checkout_cart_add' != $observer->getEvent()->getControllerAction()->getFullActionName()) {
            return;
        }
        /*
         * We added the textarea form-field "biebersdorfCustomerOrderComment"
         * in the template "checkout/onepage/agreements.phtml".
         */

        $request = Mage::app()->getRequest();
        $prId = $request->getParams();
        //$data = Mage::getSingleton('core/session')->getVisitorData();
        if (empty($prId['delivery_date'])) {
            $request->setParam('product','');
            $message = $this->__('Product can not be added without delivery date.');
            $this->_getSession()->addError($message);
            $this->getResponse()->setRedirect($prId['uenc']);
            return false;
        }
        if(!empty($prId['gift-message']))
        {
            $this->_getSession()->setGiftMessage($prId['gift-message']);
        }
        $product = Mage::getModel('catalog/product')->load($prId['product']);

        if ($product->getTypeId() == 'cofigurable') {
            return $this;
        }

        if (!$product->getHasOptions()) {
            $optionID = $this->saveProductOption($product);
        } else {
            $options = $product->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getTitle() == 'Delivery Date') {
                        $optionID = $option->getOptionId();
                    }
                }
            }
            if (empty($optionID)) {
                $optionID = $this->saveProductOption($product);
            }
        }

        $deliveryDate = $prId['delivery_date'];
        if (!empty($deliveryDate)) {
            $opt['options'] = array($optionID => $deliveryDate);
            $request->setParams($opt);
        }

        return $this;
    }

    function saveProductOption($product)
    {

        $store = Mage::app()->getStore()->getId();
        $opt = Mage::getModel('catalog/product_option');
        $opt->setProduct($product);
        $option = array(
            'is_delete' => 0,
            'is_require' => false,
            'previous_group' => 'text',
            'title' => 'Delivery Date',
            'type' => 'field',
            'price_type' => 'fixed',
            'price' => '0.0000'
        );
        $opt->addOption($option);
        $opt->saveOptions();
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
        $product->setHasOptions(1);
        $product->save();

        $pr = Mage::getModel('catalog/product')->load($product->getEntityId());
        $options = $pr->getOptions();
        if ($options) {
            foreach ($options as $option) {
                if ($option->getTitle() == 'Delivery Date') {
                    $optionID = $option->getOptionId();
                }
            }
        }
        Mage::app()->setCurrentStore(Mage::getModel('core/store')->load($store));
        return $optionID;
    }

}
