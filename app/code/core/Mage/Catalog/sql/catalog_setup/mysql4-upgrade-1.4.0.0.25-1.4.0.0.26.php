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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp')}`;
    CREATE TABLE `{$installer->getTable('catalog/product_price_indexer_cfg_option_aggregate_tmp')}` (
        `parent_id` int(10) unsigned NOT NULL,
        `child_id` int(10) unsigned NOT NULL,
        `customer_group_id` smallint(5) unsigned NOT NULL,
        `website_id` smallint(5) unsigned NOT NULL,
        `price` decimal(12,4) default NULL,
        `tier_price` decimal(12,4) default NULL,
    PRIMARY KEY  (`parent_id`,`child_id`,`customer_group_id`,`website_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
");
