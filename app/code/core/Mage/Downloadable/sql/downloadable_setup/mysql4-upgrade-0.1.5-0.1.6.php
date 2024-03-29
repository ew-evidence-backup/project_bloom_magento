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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('downloadable/link_purchased')}` (
    `purchased_id` int(10) unsigned NOT NULL auto_increment,
    `order_item_id` int(10) unsigned NOT NULL default '0',
    `order_id` int(10) unsigned NOT NULL default '0',
    `number_of_downloads_bought` int(10) unsigned NOT NULL default '0',
    `number_of_downloads_used` int(10) unsigned NOT NULL default '0',
    `link_id` int(20) unsigned NOT NULL default '0',
    `link_title` varchar(255) NOT NULL default '',
    `link_url` varchar(255) NOT NULL default '',
    `link_file` varchar(255) NOT NULL default '',
    `status` varchar(50) NOT NULL default '',
    `product_name` varchar(255) NOT NULL default '',
    `product_sku` varchar(255) NOT NULL default '',
    PRIMARY KEY  (`purchased_id`),
    KEY `DOWNLOADABLE_ORDER_ITEM_ID` (`order_item_id`),
    KEY `DOWNLOADABLE_ORDER_ID` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$conn->addConstraint(
    'FK_DOWNLOADABLE_ORDER_ITEM_ID', $installer->getTable('downloadable/link_purchased'), 'order_item_id', $installer->getTable('sales/order_item'), 'item_id'
);
$conn->addConstraint(
    'FK_DOWNLOADABLE_ORDER_ID', $installer->getTable('downloadable/link_purchased'), 'order_id', $installer->getTable('sales/order'), 'entity_id'
);

$installer->endSetup();
