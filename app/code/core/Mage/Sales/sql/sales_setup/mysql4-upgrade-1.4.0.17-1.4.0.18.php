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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/invoice_comment'), 'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`');
$installer->getConnection()->addColumn($installer->getTable('sales/shipment_comment'), 'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`');
$installer->getConnection()->addColumn($installer->getTable('sales/creditmemo_comment'), 'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`');
$installer->endSetup();
