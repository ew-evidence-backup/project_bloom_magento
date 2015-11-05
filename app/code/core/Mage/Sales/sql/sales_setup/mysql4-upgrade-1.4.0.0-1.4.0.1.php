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
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_grid'), 'updated_at', 'datetime default NULL');
$installer->getConnection()->addKey($installer->getTable('sales_flat_order_grid'), 'IDX_UPDATED_AT' ,'updated_at');
$installer->run("
    UPDATE {$installer->getTable('sales_flat_order_grid')} AS g
        JOIN {$installer->getTable('sales_flat_order')} AS o ON g.entity_id=o.entity_id
        SET g.updated_at=o.updated_at
");
