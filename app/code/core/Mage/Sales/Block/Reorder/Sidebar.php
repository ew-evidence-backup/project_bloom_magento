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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Sales order view block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Reorder_Sidebar extends Mage_Core_Block_Template
{
    /**
     * Init orders and templates
     */
    public function __construct()
    {
        parent::__construct();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->setTemplate('sales/order/history.phtml');
            $this->initOrders();
        }

    }

    /**
     * Init customer order for display on front
     */
    public function initOrders()
    {
        $customerId = $this->getCustomerId() ? $this->getCustomerId()
            : Mage::getSingleton('customer/session')->getCustomer()->getId();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToFilter('customer_id', $customerId)
            ->addAttributeToFilter('state',
                array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates())
            )
            ->addAttributeToSort('created_at', 'desc')
            ->setPage(1,1);
        //TODO: add filter by current website

        $this->setOrders($orders);
    }

    /**
     * Get list of last ordered products
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        $order = $this->getLastOrder();
        $limit = 5;

        if ($order) {
            $website = Mage::app()->getStore()->getWebsiteId();
            foreach ($order->getParentItemsRandomCollection($limit) as $item) {
                if ($item->getProduct() && in_array($website, $item->getProduct()->getWebsiteIds())) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Check item product availability for reorder
     *
     * @param  Mage_Sales_Model_Order_Item $orderItem
     * @return boolean
     */
    public function isItemAvailableForReorder(Mage_Sales_Model_Order_Item $orderItem)
    {
        if ($orderItem->getProduct()) {
            return $orderItem->getProduct()->getStockItem()->getIsInStock();
        }
        return false;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/addgroup', array('_secure' => true));
    }

    /**
     * Last order getter
     *
     * @return Mage_Sales_Model_Order | false
     */
    public function getLastOrder()
    {
        foreach ($this->getOrders() as $order) {
            return $order;
        }
        return false;
    }

    protected function _toHtml()
    {
        if (Mage::helper('sales/reorder')->isAllow()
            && (Mage::getSingleton('customer/session')->isLoggedIn() || $this->getCustomerId())
        ) {
            return parent::_toHtml();
        }
        return '';
    }
}
