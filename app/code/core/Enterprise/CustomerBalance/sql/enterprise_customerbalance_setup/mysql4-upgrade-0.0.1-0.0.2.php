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
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$installer->run("ALTER TABLE {$installer->getTable('enterprise_customerbalance')} CHANGE `website_id` `website_id` SMALLINT( 5 ) UNSIGNED NOT NULL;");

$installer->run("
ALTER TABLE {$installer->getTable('enterprise_customerbalance')} ADD KEY `FK_CUSTOMERBALANCE_CORE_WEBSITE` ( `website_id` ) ,
ADD CONSTRAINT `FK_CUSTOMERBALANCE_CORE_WEBSITE` FOREIGN KEY ( website_id ) REFERENCES {$installer->getTable('core_website')}( `website_id` ) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();
