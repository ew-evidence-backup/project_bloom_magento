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
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection->addColumn($installer->getTable('catalog/product_super_attribute_pricing'), 'website_id', 'smallint(5) UNSIGNED NOT NULL DEFAULT 0');
$connection->addConstraint('FK_CATALOG_PRODUCT_SUPER_PRICE_WEBSITE',
    $installer->getTable('catalog/product_super_attribute_pricing'), 'website_id',
    $installer->getTable('core/website'), 'website_id',
    'cascade', 'cascade'
);
$installer->endSetup();
