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
class Mage_Sales_Block_Order_Invoice_Totals extends Mage_Sales_Block_Order_Totals
{
    protected $_invoice = null;

    public function getInvoice()
    {
        if ($this->_invoice === null) {
            if ($this->hasData('invoice')) {
                $this->_invoice = $this->_getData('invoice');
            } elseif (Mage::registry('current_invoice')) {
                $this->_invoice = Mage::registry('current_invoice');
            } elseif ($this->getParentBlock()->getInvoice()) {
                $this->_invoice = $this->getParentBlock()->getInvoice();
            }
        }
        return $this->_invoice;
    }

    public function setInvoice($invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal('base_grandtotal');
        return $this;
    }


}
