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
 * @package     Mage_Sitemap
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/**
 * Create table 'sitemap'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('sitemap/sitemap'))
    ->addColumn('sitemap_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Sitemap Id')
    ->addColumn('sitemap_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Sitemap Type')
    ->addColumn('sitemap_filename', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Sitemap Filename')
    ->addColumn('sitemap_path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Sitemap Path')
    ->addColumn('sitemap_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true,
        ), 'Sitemap Time')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store id')
    ->addIndex($installer->getIdxName('sitemap/sitemap', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('sitemap/sitemap', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Google Sitemap');

$installer->getConnection()->createTable($table);

$installer->endSetup();
