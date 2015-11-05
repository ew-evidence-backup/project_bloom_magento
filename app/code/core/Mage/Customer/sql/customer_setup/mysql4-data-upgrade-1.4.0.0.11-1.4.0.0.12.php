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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$attributes = array(
    'prefix', 'firstname', 'middlename', 'lastname', 'suffix'
);

foreach ($attributes as $attributeCode) {
    $attribute   = Mage::getSingleton('eav/config')->getAttribute('customer_address', $attributeCode);
    $usedInForms = $attribute->getUsedInForms();
    if (!in_array('customer_register_address', $usedInForms)) {
        $usedInForms[] = 'customer_register_address';
        $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
}
