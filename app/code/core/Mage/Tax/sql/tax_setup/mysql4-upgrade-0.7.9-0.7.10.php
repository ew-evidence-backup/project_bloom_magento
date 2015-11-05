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

$installer = $this;
/* @var $installer Mage_Tax_Model_Mysql4_Setup */

$installer->startSetup();

$table = $installer->getTable('tax_calculation_rate');

$installer->getConnection()->addColumn($table, 'zip_is_range', "TINYINT(1) DEFAULT NULL");
$installer->getConnection()->addColumn($table, 'zip_from', "VARCHAR(10) DEFAULT NULL");
$installer->getConnection()->addColumn($table, 'zip_to', "VARCHAR(10) DEFAULT NULL");

$installer->getConnection()->addKey($table, 'IDX_TAX_CALCULATION_RATE_RANGE', array('tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode'));

$installer->endSetup();
