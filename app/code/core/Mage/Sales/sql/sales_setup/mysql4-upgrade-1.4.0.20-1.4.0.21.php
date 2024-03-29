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

// Setup data to configure
$frequencies = array(
    Mage_Sales_Model_Mysql4_Report_Bestsellers::AGGREGATION_DAILY,
    Mage_Sales_Model_Mysql4_Report_Bestsellers::AGGREGATION_MONTHLY,
    Mage_Sales_Model_Mysql4_Report_Bestsellers::AGGREGATION_YEARLY
);

$foreignKeys = array(
    array(
        'name' => 'FK_PRODUCT_ORDERED_AGGREGATED_%s_STORE_ID',
        'column' => 'store_id',
        'refTable' => 'core/store',
        'refColumn' => 'store_id'
    ),
    array(
        'name' => 'FK_PRODUCT_ORDERED_AGGREGATED_%s_PRODUCT_ID',
        'column' => 'product_id',
        'refTable' => 'catalog/product',
        'refColumn' => 'entity_id'
    )
);

/*
 * Alter foreign keys to add 'CASCADE' instead of 'SET_NULL' action
 * Also remove all wrong report records with NULL in 'product_id' field
 */
$connection = $installer->getConnection();
foreach ($frequencies as $frequency) {
    $tableName = $installer->getTable('sales/bestsellers_aggregated_' . $frequency);

    foreach ($foreignKeys as $fkInfo) {
        $connection->addConstraint(
            sprintf($fkInfo['name'], strtoupper($frequency)),
            $tableName,
            $fkInfo['column'],
            $installer->getTable($fkInfo['refTable']),
            $fkInfo['refColumn']
        );
    }

    $connection->delete($tableName, 'product_id IS NULL');
}
