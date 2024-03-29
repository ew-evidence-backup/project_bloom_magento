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
// make attribute 'weight' not applicable to downloadable products
$applyTo = explode(',', $installer->getAttribute('catalog_product', 'weight', 'apply_to'));
if (in_array('downloadable', $applyTo)) {
    $newApplyTo = array();
    foreach ($applyTo as $key=>$value) {
        if ($value != 'downloadable') {
            $newApplyTo[] = $value;
        }
    }
    $installer->updateAttribute('catalog_product', 'weight', 'apply_to', join(',', $newApplyTo));
} else {
    $installer->updateAttribute('catalog_product', 'weight', 'apply_to', join(',', $applyTo));
}

// remove 'weight' values for downloadable products if there were any created
$attributeId = $installer->getAttributeId('catalog_product', 'weight');
$installer->run("
    DELETE FROM {$installer->getTable('catalog_product_entity_decimal')}
    WHERE (entity_id in (
        SELECT entity_id FROM {$installer->getTable('catalog/product')} WHERE type_id = 'downloadable'
    )) and attribute_id = {$attributeId}
");

$installer->endSetup();
