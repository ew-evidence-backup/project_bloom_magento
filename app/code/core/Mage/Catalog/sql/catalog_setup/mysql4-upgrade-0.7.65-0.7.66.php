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

Mage::getModel('catalog/product_flat_flag')
    ->loadSelf()
    ->setIsBuild(false)
    ->save();

$installer->startSetup();
$installer->run("
    UPDATE `{$installer->getTable('core/config_data')}` SET `value`=0
        WHERE `path` LIKE '".Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT."';
");
$installer->endSetup();