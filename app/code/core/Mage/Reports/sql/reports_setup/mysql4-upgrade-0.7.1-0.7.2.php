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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Report events SQL
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

$installer = $this;
/* $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('report_event_types')} DROP INDEX `event_type_id`;
ALTER TABLE {$this->getTable('report_event_types')} CHANGE `event_name` `event_name` varchar(64) NOT NULL;    
UPDATE {$this->getTable('report_event_types')} SET `event_name`='catalog_product_compare_add_product' WHERE `event_type_id`=3;
ALTER TABLE {$this->getTable('report_event')} ADD `sybtype` tinyint(3) unsigned NOT NULL default '0' AFTER `subject_id`;
ALTER TABLE {$this->getTable('report_event')} ADD INDEX (`event_type_id`); 
ALTER TABLE {$this->getTable('report_event')} ADD INDEX (`sybtype`);
ALTER TABLE {$this->getTable('report_event')} ADD INDEX (`store_id`);
ALTER TABLE {$this->getTable('report_event_types')} ADD `customer_login` TINYINT UNSIGNED NOT NULL DEFAULT '0';
UPDATE {$this->getTable('report_event_types')} SET `customer_login`=1;
");

$installer->endSetup();
