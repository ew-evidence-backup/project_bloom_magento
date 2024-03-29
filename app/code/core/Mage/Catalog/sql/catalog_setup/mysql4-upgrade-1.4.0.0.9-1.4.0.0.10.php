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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$table = $this->getTable('catalog/category_product_index');

/**
 * Remove data duplicates
 */
$stmt = $installer->getConnection()->query(
    'SELECT * FROM ' . $table . ' GROUP BY category_id, product_id, store_id HAVING count(*)>1'
);

while ($row = $stmt->fetch()) {
    $condition = 'category_id=' . $row['category_id']
        . ' AND product_id=' . $row['product_id']
        . ' AND store_id=' . $row['store_id'] . ' AND is_parent=0';
    $installer->getConnection()->delete($table, $condition);
}

$installer->getConnection()->addKey(
    $table,
    'UNQ_CATEGORY_PRODUCT',
    array('category_id', 'product_id', 'store_id'),
    'unique'
);
