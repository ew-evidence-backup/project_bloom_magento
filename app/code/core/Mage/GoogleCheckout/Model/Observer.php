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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Google Checkout Event Observer
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 */
class Mage_GoogleCheckout_Model_Observer
{
    public function salesOrderShipmentTrackSaveAfter(Varien_Event_Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();

        $order = $track->getShipment()->getOrder();

        if ($order->getShippingMethod()!='googlecheckout_carrier') {
            return;
        }

        Mage::getModel('googlecheckout/api')
            ->setStoreId($order->getStoreId())
            ->deliver($order->getExtOrderId(), $track->getCarrierCode(), $track->getNumber());
    }

    /*
     * Performs specifical actions on Google Checkout internal shipments saving
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
        if (!$shippingMethod) {
            return;
        }

        // Process only Google Checkout internal methods
        /* @var $gcCarrier Mage_GoogleCheckout_Model_Shipping */
        $gcCarrier = Mage::getModel('googlecheckout/shipping');
        list($carrierCode, $methodCode) = explode('_', $shippingMethod);
        if ($gcCarrier->getCarrierCode() != $carrierCode) {
            return;
        }
        $internalMethods = $gcCarrier->getInternallyAllowedMethods();
        if (!isset($internalMethods[$methodCode])) {
            return;
        }

        // Process this saving
        $items = array();
        foreach ($shipment->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItemId()) {
                continue;
            }
            $items[] = $item->getSku();
        }

        if ($items) {
            Mage::getModel('googlecheckout/api')
                ->setStoreId($order->getStoreId())
                ->shipItems($order->getExtOrderId(), $items);
        }
    }
}
