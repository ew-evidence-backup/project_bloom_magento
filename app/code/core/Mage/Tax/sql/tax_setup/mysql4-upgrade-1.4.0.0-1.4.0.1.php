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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/**
 * Modify tax report aggregation table to have the tax percent field as part of unique key,
 * because tax rate can be changed (code will remain the same) and it will not be reflected in statistics
 * Data has to be truncated to avoid possible duplicates and then reaggregated to reflect the correct data
 */
$table = $installer->getTable('tax/tax_order_aggregated_created');
$installer->getConnection()->truncate($table);
$installer->getConnection()->addKey($table, 'UNQ_PERIOD_STORE_CODE_ORDER_STATUS',
    array('period', 'store_id', 'code', 'percent', 'order_status'), 'unique'
);
