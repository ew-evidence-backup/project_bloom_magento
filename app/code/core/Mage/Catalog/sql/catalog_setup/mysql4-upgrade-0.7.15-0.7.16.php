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
    ALTER TABLE `{$installer->getTable('catalog_product_entity')}`
        CHANGE `type_id` `type_id` VARCHAR(32) DEFAULT 'simple' NOT NULL;
    UPDATE `{$installer->getTable('catalog_product_entity')}`
        SET `type_id` = CASE `type_id`
            WHEN '1' THEN 'simple'
            WHEN '2' THEN 'bundle'
            WHEN '3' THEN 'configurable'
            WHEN '4' THEN 'grouped'
            WHEN '5' THEN 'virtual'
            ELSE `type_id` END;
");
$installer->endSetup();
