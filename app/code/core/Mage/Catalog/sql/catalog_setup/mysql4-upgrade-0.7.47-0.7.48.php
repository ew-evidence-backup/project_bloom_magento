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

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('catalog/product')}` ADD `has_options` SMALLINT(1) NOT NULL DEFAULT '0';
");

$installer->addAttribute('catalog_product', 'has_options', array(
    'type' => 'static',
    'visible'=>false,
    'default' => false
));
$installer->run("
    UPDATE `{$installer->getTable('catalog/product')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT product_id FROM `{$installer->getTable('catalog/product_option')}` GROUP BY product_id
    ));
    UPDATE `{$installer->getTable('catalog/product')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT product_id FROM `{$installer->getTable('catalog/product_super_attribute')}` GROUP BY product_id
    ));
");

$installer->endSetup();
