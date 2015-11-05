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

$installer = $this;
/* @var $installer Mage_GoogleCheckout_Model_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('catalog_product', 'disable_googlecheckout', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Not available for purchase with Google Checkout',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => '',
        'is_configurable'   => false
    ));

$attributeId = $installer->getAttributeId('catalog_product', 'disable_googlecheckout');

foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
    try {
        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'Prices');
    } catch (Exception $e) {
        $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
    }
    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
}
$installer->endSetup();
