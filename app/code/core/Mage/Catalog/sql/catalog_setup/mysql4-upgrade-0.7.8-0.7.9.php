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
    DROP TABLE IF EXISTS `{$this->getTable('catalog_product_entity_media_gallery_image')}`;
");

$installer->getConnection()->dropColumn($this->getTable('catalog_product_entity_media_gallery'), 'entity_type_id');

$installer->updateAttribute('catalog_product', 'image', 'frontend_input', 'media_image');
$installer->updateAttribute('catalog_product', 'small_image', 'frontend_input', 'media_image');
$installer->updateAttribute('catalog_product', 'thumbnail', 'frontend_input', 'media_image');
$installer->endSetup();
