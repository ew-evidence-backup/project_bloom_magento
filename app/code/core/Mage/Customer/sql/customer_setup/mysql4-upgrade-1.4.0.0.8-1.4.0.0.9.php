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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
CREATE TABLE `{$installer->getTable('customer/eav_attribute_website')}` (
  `attribute_id` smallint(5) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL,
  `is_visible` tinyint(1) unsigned DEFAULT NULL,
  `is_required` tinyint(1) unsigned DEFAULT NULL,
  `default_value` text,
  `multiline_count` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`attribute_id`, `website_id`),
  KEY `IDX_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_ATTRIBUTE_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
    REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_WEBSITE_CORE_WEBSITE` FOREIGN KEY (`website_id`)
    REFERENCES `{$installer->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
