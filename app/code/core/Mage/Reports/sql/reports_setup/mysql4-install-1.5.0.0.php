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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;


$installFile = dirname(__FILE__) . DS . 'install-1.6.0.0.php';
if (file_exists($installFile)) {
    include $installFile;

    /**
     * Unique indexes for reports/viewed_product_index
     */
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/viewed_product_index'),
        $installer->getIdxName(
            'reports/viewed_product_index',
            array('visitor_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('visitor_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/viewed_product_index'),
        $installer->getIdxName(
            'reports/viewed_product_index',
            array('customer_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('customer_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

    /**
     * Unique indexes for reports/compared_product_index
     */
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/compared_product_index'),
        $installer->getIdxName(
            'reports/compared_product_index',
            array('visitor_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('visitor_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/compared_product_index'),
        $installer->getIdxName(
            'reports/compared_product_index',
            array('customer_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('customer_id', 'product_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
}
