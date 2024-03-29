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
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection->beginTransaction();
try {
    $installer->run("
    CREATE TABLE `{$installer->getTable('cataloginventory_stock_status')}` (
      `product_id` int(10) unsigned NOT NULL,
      `website_id` smallint(5) unsigned NOT NULL,
      `stock_id` smallint(4) unsigned NOT NULL,
      `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
      `stock_status` tinyint(3) unsigned NOT NULL,
      PRIMARY KEY (`product_id`,`website_id`,`stock_id`),
      CONSTRAINT `FK_CATALOGINVENTORY_STOCK_STATUS_STOCK` FOREIGN KEY (`stock_id`) REFERENCES `{$installer->getTable('cataloginventory_stock')}` (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOGINVENTORY_STOCK_STATUS_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    Mage::getModel('cataloginventory/stock_status')->rebuild();
}
catch (Exception $e) {
    $connection->rollBack();
    throw $e;
}
$connection->commit();
$installer->endSetup();
