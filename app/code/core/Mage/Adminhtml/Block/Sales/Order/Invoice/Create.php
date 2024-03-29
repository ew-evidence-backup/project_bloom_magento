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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Adminhtml invoice create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Invoice_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_invoice';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return ($this->getInvoice()->getOrder()->getForcedDoShipmentWithInvoice())
            ? Mage::helper('sales')->__('New Invoice and Shipment for Order #%s', $this->getInvoice()->getOrder()->getRealOrderId())
            : Mage::helper('sales')->__('New Invoice for Order #%s', $this->getInvoice()->getOrder()->getRealOrderId());
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$this->getInvoice()->getOrderId()));
    }
}
