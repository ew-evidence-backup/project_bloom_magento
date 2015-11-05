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

$orderHistoryTable = $installer->getTable('sales_flat_order_status_history');
$installer->getConnection()->addColumn(
    $orderHistoryTable,
    'is_visible_on_front',
    "tinyint(1) UNSIGNED NOT NULL default '0' after `is_customer_notified`"
);
$installer->run("UPDATE {$orderHistoryTable} SET
    is_visible_on_front = (is_customer_notified = 1 AND comment IS NOT NULL AND comment <> '');"
);
