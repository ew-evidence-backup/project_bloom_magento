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
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_super_attribute_label'),
    'IDX_CATALOG_PRODUCT_SUPER_ATTRIBUTE_STORE_PSAI_SI',
    array('product_super_attribute_id', 'store_id'));

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_price'),
    'IDX_CATALOG_PRODUCT_OPTION_PRICE_SI_OI',
    array('store_id', 'option_id'));

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TITLE_SI_OI',
    array('store_id', 'option_id'));

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_type_price'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_SI_OTI',
    array('store_id', 'option_type_id'));

$installer->getConnection()->addKey(
    $installer->getTable('catalog_product_option_type_title'),
    'IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_SI_OTI',
    array('store_id', 'option_type_id'));

$installer->endSetup();
