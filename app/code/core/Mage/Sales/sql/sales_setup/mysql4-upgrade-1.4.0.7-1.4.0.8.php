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

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$orderTable = $installer->getTable('sales/order');

$installer->run("
UPDATE {$orderTable} SET
    base_discount_canceled = (ABS(base_discount_amount) - IFNULL(base_discount_invoiced, 0)),
    base_total_canceled = (base_subtotal_canceled + IFNULL(base_tax_canceled, 0) + IFNULL(base_shipping_canceled, 0) - IFNULL(ABS(base_discount_amount) - IFNULL(base_discount_invoiced, 0), 0)),
    discount_canceled = (ABS(discount_amount) - IFNULL(discount_invoiced, 0)),
    total_canceled = (subtotal_canceled + IFNULL(tax_canceled, 0) + IFNULL(shipping_canceled, 0) - IFNULL(ABS(discount_amount) - IFNULL(discount_invoiced, 0), 0))
");
