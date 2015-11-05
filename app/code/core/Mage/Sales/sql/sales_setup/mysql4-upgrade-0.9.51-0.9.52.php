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

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$tableOrder         = $this->getTable('sales_order');
$tableOrderItem     = $this->getTable('sales_flat_order_item');

$select = $installer->getConnection()->select()
    ->from($tableOrderItem, array(
        'total_qty_ordered'   => 'SUM(qty_ordered)',
        'entity_id'           => 'order_id'))
    ->group(array('order_id'));

$installer->run('CREATE TEMPORARY TABLE `tmp_order_items` ' . $select->assemble());

$select->reset()
    ->join('tmp_order_items', 'tmp_order_items.entity_id = order.entity_id', array('total_qty_ordered', 'entity_id'));
$sqlQuery = $select->crossUpdateFromSelect(array('order' => $tableOrder));
$installer->getConnection()->query($sqlQuery);

$installer->run('DROP TEMPORARY TABLE `tmp_order_items`');
