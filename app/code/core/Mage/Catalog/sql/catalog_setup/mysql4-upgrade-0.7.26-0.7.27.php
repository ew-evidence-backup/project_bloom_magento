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
$conn = $installer->getConnection();
$conn->addColumn($installer->getTable('core_url_rewrite'), 'category_id', 'int unsigned NULL AFTER `store_id`');
$conn->addColumn($installer->getTable('core_url_rewrite'), 'product_id', 'int unsigned NULL AFTER `category_id`');
$installer->run("
UPDATE `{$installer->getTable('core_url_rewrite')}`
    SET `category_id`=SUBSTRING_INDEX(SUBSTR(`id_path` FROM 10),'/',1)
    WHERE `id_path` LIKE 'category/%';
UPDATE `{$installer->getTable('core_url_rewrite')}`
    SET `product_id`=SUBSTRING_INDEX(SUBSTR(`id_path` FROM 9),'/',1)
    WHERE `id_path` RLIKE 'product/[0-9]+$';
UPDATE `{$installer->getTable('core_url_rewrite')}`
    SET `category_id`=SUBSTRING_INDEX(SUBSTR(`id_path` FROM 9),'/',-1),
    `product_id`=SUBSTRING_INDEX(SUBSTR(`id_path` FROM 9),'/',1)
    WHERE `id_path` LIKE 'product/%/%';

DROP TABLE IF EXISTS `{$installer->getTable('core_url_rewrite_temporary')}`;
CREATE TABLE `{$installer->getTable('core_url_rewrite_temporary')}` (
  `url_rewrite_id` int unsigned not null,
  PRIMARY KEY(`url_rewrite_id`)
) ENGINE=MyISAM;

REPLACE INTO `{$installer->getTable('core_url_rewrite_temporary')}` (`url_rewrite_id`)
    SELECT `ur`.`url_rewrite_id` FROM `{$installer->getTable('core_url_rewrite')}` as `ur`
            LEFT JOIN `{$installer->getTable('catalog_category_entity')}` as `cc` ON `ur`.`category_id`=`cc`.`entity_id`
        WHERE `ur`.`category_id` IS NOT NULL
            AND `cc`.`entity_id` IS NULL;
REPLACE INTO `{$installer->getTable('core_url_rewrite_temporary')}` (`url_rewrite_id`)
    SELECT `ur`.`url_rewrite_id` FROM `{$installer->getTable('core_url_rewrite')}` as `ur`
        LEFT JOIN `{$installer->getTable('catalog_product_entity')}` as `cp` ON `ur`.`product_id`=`cp`.`entity_id`
        WHERE `ur`.`product_id` IS NOT NULL
            AND `cp`.`entity_id` IS NULL;
DELETE FROM `{$installer->getTable('core_url_rewrite')}` WHERE `url_rewrite_id` IN(
    SELECT `url_rewrite_id` FROM `{$installer->getTable('core_url_rewrite_temporary')}`
);
DROP TABLE IF EXISTS `{$installer->getTable('core_url_rewrite_temporary')}`;
");
$conn->addConstraint(
    'FK_CORE_URL_REWRITE_CATEGORY', $installer->getTable('core_url_rewrite'), 'category_id',
    $installer->getTable('catalog_category_entity'), 'entity_id'
);
$conn->addConstraint(
    'FK_CORE_URL_REWRITE_PRODUCT', $installer->getTable('core_url_rewrite'), 'product_id',
    $installer->getTable('catalog_product_entity'), 'entity_id'
);
$installer->endSetup();
