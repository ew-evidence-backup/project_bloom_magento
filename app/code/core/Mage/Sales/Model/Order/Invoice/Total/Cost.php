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


class Mage_Sales_Model_Order_Invoice_Total_Cost extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect total cost of invoiced items
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Invoice_Total_Cost
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $baseInvoiceTotalCost = 0;
        foreach ($invoice->getAllItems() as $item) {
            if (!$item->getHasChildren()){
                $baseInvoiceTotalCost += $item->getBaseCost()*$item->getQty();
            }
        }
        $invoice->setBaseCost($baseInvoiceTotalCost);
        return $this;
    }
}
