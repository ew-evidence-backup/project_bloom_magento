<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Sales orders controller
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Shipping_TrackingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Ajax action
     *
     */
    public function ajaxAction()
    {
        if ($order = $this->_initOrder()) {
            $response = '';
            $tracks = $order->getTracksCollection();

            $className = Mage::getConfig()->getBlockClassName('core/template');
            $block = new $className();
            $block->setType('core/template')
                ->setIsAnonymous(true)
                ->setTemplate('sales/order/trackinginfo.phtml');

            foreach ($tracks as $track){
                $trackingInfo = $track->getNumberDetail();
                $block->setTrackingInfo($trackingInfo);
                $response .= $block->toHtml()."\n<br />";
            }

            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Popup action
     * Shows tracking info if it's present, otherwise redirects to 404
     */
    public function popupAction()
    {
        $shippingInfoModel = Mage::getModel('shipping/info')->loadByHash($this->getRequest()->getParam('hash'));
				
/*				var_dump($shippingInfoModel);
				var_dump($this->getRequest()->getParam('hash'));
				die();*/
				
        Mage::register('current_shipping_info', $shippingInfoModel);
        if (count($shippingInfoModel->getTrackingInfo()) == 0) {
            $this->norouteAction();
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');

        $order = Mage::getModel('sales/order')->load($id);
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();

        if (!$order->getId() || !$customerId || $order->getCustomerId() != $customerId) {
            return false;
        }
        return $order;
    }

}
