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


class Mage_Sales_Model_Order_Invoice_Total_Subtotal extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Total_Subtotal
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $subtotal       = 0;
        $baseSubtotal   = 0;
        $subtotalInclTax= 0;
        $baseSubtotalInclTax = 0;

        $order = $invoice->getOrder();

        foreach ($invoice->getAllItems() as $item) {
            $item->calcRowTotal();

            if ($item->getOrderItem()->isDummy()) {
                continue;
            }

            $subtotal       += $item->getRowTotal();
            $baseSubtotal   += $item->getBaseRowTotal();
            $subtotalInclTax+= $item->getRowTotalInclTax();
            $baseSubtotalInclTax += $item->getBaseRowTotalInclTax();
        }

        $allowedSubtotal = $order->getSubtotal() - $order->getSubtotalInvoiced();
        $baseAllowedSubtotal = $order->getBaseSubtotal() -$order->getBaseSubtotalInvoiced();
        $allowedSubtotalInclTax = $allowedSubtotal + $order->getHiddenTaxAmount()
                + $order->getTaxAmount() - $order->getTaxInvoiced() - $order->getShippingTaxAmount();
        $baseAllowedSubtotalInclTax = $baseAllowedSubtotal + $order->getBaseHiddenTaxAmount()
                + $order->getBaseTaxAmount() - $order->getBaseTaxInvoiced() - $order->getBaseShippingTaxAmount();

        if ($invoice->isLast()) {
            $subtotal = $allowedSubtotal;
            $baseSubtotal = $baseAllowedSubtotal;
            $subtotalInclTax = $allowedSubtotalInclTax;
            $baseSubtotalInclTax  = $baseAllowedSubtotalInclTax;
        } else {
            $subtotal = min($allowedSubtotal, $subtotal);
            $baseSubtotal = min($baseAllowedSubtotal, $baseSubtotal);
            $subtotalInclTax = min($allowedSubtotalInclTax, $subtotalInclTax);
            $baseSubtotalInclTax = min($baseAllowedSubtotalInclTax, $baseSubtotalInclTax);
        }

        $invoice->setSubtotal($subtotal);
        $invoice->setBaseSubtotal($baseSubtotal);
        $invoice->setSubtotalInclTax($subtotalInclTax);
        $invoice->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $subtotal);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseSubtotal);
        return $this;
    }
}
