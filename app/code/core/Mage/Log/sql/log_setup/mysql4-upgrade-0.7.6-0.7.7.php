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
 * @package     Mage_Log
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('log/customer')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/quote_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/summary_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/summary_type_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/url_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/url_info_table')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/visitor')}` ENGINE INNODB;
    ALTER TABLE `{$installer->getTable('log/visitor_info')}` ENGINE INNODB;
");

$installer->endSetup();
