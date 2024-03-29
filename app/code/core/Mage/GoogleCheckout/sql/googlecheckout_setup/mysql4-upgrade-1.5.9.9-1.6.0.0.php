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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_GoogleCheckout_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Change columns
 */
$tables = array(
    $installer->getTable('googlecheckout/notification') => array(
        'columns' => array(
            'serial_number' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Serial Number'
            ),
            'started_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Started At'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            )
        ),
        'comment' => 'Google Checkout Notification Table'
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->endSetup();
