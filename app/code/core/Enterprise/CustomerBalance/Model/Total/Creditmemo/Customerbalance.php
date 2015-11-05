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
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Enterprise_CustomerBalance_Model_Total_Creditmemo_Customerbalance extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect customer balance totals for credit memo
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Enterprise_CustomerBalance_Model_Total_Creditmemo_Customerbalance
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $creditmemo->setBsCustomerBalTotalRefunded(0);
        $creditmemo->setCustomerBalTotalRefunded(0);

        $creditmemo->setBaseCustomerBalanceReturnMax(0);
        $creditmemo->setCustomerBalanceReturnMax(0);

        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
            return $this;
        }

        $order = $creditmemo->getOrder();
        if ($order->getBaseCustomerBalanceAmount() && $order->getBaseCustomerBalanceInvoiced() != 0) {
            $cbLeft = $order->getBaseCustomerBalanceInvoiced() - $order->getBaseCustomerBalanceRefunded();

            $used = 0;
            $baseUsed = 0;

            if ($cbLeft >= $creditmemo->getBaseGrandTotal()) {
                $baseUsed = $creditmemo->getBaseGrandTotal();
                $used = $creditmemo->getGrandTotal();

                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setGrandTotal(0);

                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $baseUsed = $order->getBaseCustomerBalanceInvoiced() - $order->getBaseCustomerBalanceRefunded();
                $used = $order->getCustomerBalanceInvoiced() - $order->getCustomerBalanceRefunded();

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
            }

            $creditmemo->setBaseCustomerBalanceAmount($baseUsed);
            $creditmemo->setCustomerBalanceAmount($used);
        }

        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseGrandTotal());
        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseCustomerBalanceAmount());

        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getCustomerBalanceAmount());

        return $this;
    }
}
