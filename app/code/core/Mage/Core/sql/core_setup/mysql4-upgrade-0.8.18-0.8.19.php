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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
    ALTER TABLE `{$installer->getTable('core_email_variable')}` RENAME TO `{$installer->getTable('core/variable')}`;
    ALTER TABLE `{$installer->getTable('core_email_variable_value')}` RENAME TO `{$installer->getTable('core/variable_value')}`;
");

$installer->getConnection()->dropForeignKey($installer->getTable('core/variable_value'), 'FK_CORE_EMAIL_VARIABLE_VALUE_STORE_ID');
$installer->getConnection()->dropForeignKey($installer->getTable('core/variable_value'), 'FK_CORE_EMAIL_VARIABLE_VALUE_VARIABLE_ID');

$installer->getConnection()->addConstraint('FK_CORE_VARIABLE_VALUE_STORE_ID', $installer->getTable('core/variable_value'),
    'store_id', $installer->getTable('core/store'), 'store_id');
$installer->getConnection()->addConstraint('FK_CORE_VARIABLE_VALUE_VARIABLE_ID', $installer->getTable('core/variable_value'),
    'variable_id', $installer->getTable('core/variable'), 'variable_id');

$installer->endSetup();
