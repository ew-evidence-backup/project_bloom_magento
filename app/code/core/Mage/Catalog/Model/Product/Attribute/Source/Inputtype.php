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

/**
 * Product attribute source input types
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */class Mage_Catalog_Model_Product_Attribute_Source_Inputtype extends Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    /**
     * Get product input types as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $inputTypes = array(
            array(
                'value' => 'price',
                'label' => Mage::helper('catalog')->__('Price')
            ),
            array(
                'value' => 'media_image',
                'label' => Mage::helper('catalog')->__('Media Image')
            )
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_product_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }

        if (Mage::registry('attribute_type_hidden_fields') === null) {
            Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        }
        if (Mage::registry('attribute_type_disabled_types') === null) {
            Mage::register('attribute_type_disabled_types', $_disabledTypes);
        }

        return array_merge(parent::toOptionArray(), $inputTypes);
    }
}
