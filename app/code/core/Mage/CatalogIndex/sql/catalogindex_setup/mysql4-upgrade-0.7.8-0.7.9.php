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
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('catalogindex_price'), 'store_id');
$installer->getConnection()->dropColumn($installer->getTable('catalogindex_minimal_price'), 'store_id');

$installer->getConnection()->addConstraint('FK_CI_PRICE_WEBSITE_ID', $installer->getTable('catalogindex_price'), 'website_id', $installer->getTable('core_website'), 'website_id');
$installer->getConnection()->addConstraint('FK_CI_MINIMAL_PRICE_WEBSITE_ID', $installer->getTable('catalogindex_minimal_price'), 'website_id', $installer->getTable('core_website'), 'website_id');

$installer->getConnection()->addKey($installer->getTable('catalogindex_price'), 'IDX_FULL', array('entity_id', 'attribute_id', 'customer_group_id', 'value', 'website_id'));
$installer->getConnection()->addKey($installer->getTable('catalogindex_minimal_price'), 'IDX_FULL', array('entity_id', 'qty', 'customer_group_id', 'value', 'website_id'));

$installer->endSetup();
