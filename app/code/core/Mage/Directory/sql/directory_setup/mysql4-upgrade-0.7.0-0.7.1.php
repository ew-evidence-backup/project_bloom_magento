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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup()
    ->run("
DROP TABLE IF EXISTS {$this->getTable('directory_country_format')};
CREATE TABLE {$this->getTable('directory_country_format')} (
    `country_format_id` int(10) unsigned NOT NULL auto_increment,
    `country_id` char(2) NOT NULL default '',
    `type` varchar(30) NOT NULL default '',
    `format` text NOT NULL,
    PRIMARY KEY  (`country_format_id`),
    UNIQUE KEY `country_type` (`country_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries format';

ALTER TABLE {$this->getTable('directory_country')}, DROP COLUMN `address_template_plain`, DROP COLUMN `address_template_html`;
");
