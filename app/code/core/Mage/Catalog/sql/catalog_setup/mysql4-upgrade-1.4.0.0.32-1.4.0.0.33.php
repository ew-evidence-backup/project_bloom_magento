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

$installer->startSetup();

$categoryEntityTypeId = $installer->getEntityTypeId('catalog_category');
$productEntityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($categoryEntityTypeId, 'description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($categoryEntityTypeId, 'description', 'is_html_allowed_on_front', 1);
$installer->updateAttribute($productEntityTypeId, 'description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($productEntityTypeId, 'description', 'is_html_allowed_on_front', 1);
$installer->updateAttribute($productEntityTypeId, 'short_description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($productEntityTypeId, 'short_description', 'is_html_allowed_on_front', 1);

$installer->endSetup();