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
try {
    $installer->run("
        ALTER TABLE `{$installer->getTable('catalog_product_website')}` ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

        delete from `{$installer->getTable('catalog_product_website')}` where product_id not in (select entity_id from catalog_product_entity);
        delete from `{$installer->getTable('catalog_product_website')}` where website_id not in (select website_id from core_website);

        ALTER TABLE `{$installer->getTable('catalog_product_website')}` DROP INDEX `FK_CATALOG_PRODUCT_WEBSITE_WEBSITE`,
            ADD CONSTRAINT `FK_CATALOG_PRODUCT_WEBSITE_PRODUCT` FOREIGN KEY `FK_CATALOG_PRODUCT_WEBSITE_PRODUCT` (`product_id`)
             REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE` FOREIGN KEY `FK_CATAOLOG_PRODUCT_WEBSITE_WEBSITE` (`website_id`)
             REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ROW_FORMAT = FIXED;
    ");
} catch (Exception $e) {
}
$installer->endSetup();
