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

$tableBalance = $installer->getTable('enterprise_customerbalance/balance');
$installer->run("
    DELETE FROM {$tableBalance} WHERE website_id IS NULL;
");

$installer->getConnection()->dropForeignKey($tableBalance, 'FK_CUSTOMERBALANCE_WEBSITE');
$installer->getConnection()->dropKey($tableBalance, 'FK_CUSTOMERBALANCE_WEBSITE');

$installer->getConnection()->changeColumn($tableBalance, 'website_id', 'website_id', 'smallint(5) unsigned NOT NULL DEFAULT 0');

$installer->getConnection()->addConstraint('FK_CUSTOMERBALANCE_WEBSITE', $tableBalance, 'website_id', $installer->getTable('core/website'), 'website_id');

$installer->endSetup();
