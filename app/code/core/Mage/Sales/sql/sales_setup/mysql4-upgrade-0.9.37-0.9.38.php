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


/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$installer->run("
UPDATE `{$installer->getTable('sales_order')}` AS `s`
    LEFT JOIN `{$installer->getTable('customer_entity')}` AS `c`
        ON `s`.`customer_id`=`c`.`entity_id`
    SET `s`.`customer_id`=NULL
WHERE `c`.`entity_id` IS NULL;
");
$installer->getConnection()->modifyColumn($installer->getTable('sales_order'), 'customer_id', 'INT UNSIGNED NULL DEFAULT NULL');
$installer->getConnection()->addConstraint('FK_SALES_ORDER_CUSTOMER',
    $installer->getTable('sales_order'), 'customer_id',
    $installer->getTable('customer_entity'), 'entity_id',
    'set null', 'cascade'
);

$installer->endSetup();
