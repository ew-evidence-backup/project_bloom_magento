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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('cataloginventory/stock_status')}_idx`;

CREATE TABLE `{$installer->getTable('cataloginventory/stock_status_indexer_idx')}` (
     `product_id` int(10) unsigned NOT NULL,
     `website_id` smallint(5) unsigned NOT NULL,
     `stock_id` smallint(4) unsigned NOT NULL,
     `qty` decimal(12,4) NOT NULL default '0.0000',
     `stock_status` tinyint(3) unsigned NOT NULL,
     PRIMARY KEY  (`product_id`,`website_id`,`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_STOCK` (`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('cataloginventory/stock_status_indexer_tmp')}` (
     `product_id` int(10) unsigned NOT NULL,
     `website_id` smallint(5) unsigned NOT NULL,
     `stock_id` smallint(4) unsigned NOT NULL,
     `qty` decimal(12,4) NOT NULL default '0.0000',
     `stock_status` tinyint(3) unsigned NOT NULL,
     PRIMARY KEY  (`product_id`,`website_id`,`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_STOCK` (`stock_id`),
     KEY `FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE` (`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
");
$installer->endSetup();