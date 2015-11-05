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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('downloadable/link_purchased_item'), 'link_hash', "varchar(255) NOT NULL default '' AFTER `product_id`");

$installer->getConnection()->addKey($installer->getTable('downloadable/link_purchased_item'), 'DOWNLOADALBE_LINK_HASH', 'link_hash');

$select = $installer->getConnection()->select()
    ->from($installer->getTable('downloadable/link_purchased_item'), array(
        'item_id',
        'purchased_id',
        'order_item_id',
        'product_id'
    ));
$result = $installer->getConnection()->fetchAll($select);

foreach ($result as $row) {
    $installer->getConnection()->update(
        $installer->getTable('downloadable/link_purchased_item'),
        array('link_hash' => strtr(base64_encode(microtime() . $row['purchased_id'] . $row['order_item_id'] . $row['product_id']), '+/=', '-_,')),
        $installer->getConnection()->quoteInto('item_id = ?', $row['item_id'])
    );
}

$installer->endSetup();
