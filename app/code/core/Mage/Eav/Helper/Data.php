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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Eav data helper
 */
class Mage_Eav_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML path to input types validator data in config
     */
    const XML_PATH_VALIDATOR_DATA_INPUT_TYPES = 'general/validator_data/input_types';

    protected $_attributesLockedFields = array();

    protected $_entityTypeFrontendClasses = array();

    /**
     * Return default frontend classes value labal array
     *
     * @return array
     */
    protected function _getDefaultFrontendClasses()
    {
        return array(
            array(
                'value' => '',
                'label' => Mage::helper('eav')->__('None')
            ),
            array(
                'value' => 'validate-number',
                'label' => Mage::helper('eav')->__('Decimal Number')
            ),
            array(
                'value' => 'validate-digits',
                'label' => Mage::helper('eav')->__('Integer Number')
            ),
            array(
                'value' => 'validate-email',
                'label' => Mage::helper('eav')->__('Email')
            ),
            array(
                'value' => 'validate-url',
                'label' => Mage::helper('eav')->__('URL')
            ),
            array(
                'value' => 'validate-alpha',
                'label' => Mage::helper('eav')->__('Letters')
            ),
            array(
                'value' => 'validate-alphanum',
                'label' => Mage::helper('eav')->__('Letters (a-z, A-Z) or Numbers (0-9)')
            )
        );
    }

    /**
     * Return merged default and entity type frontend classes value label array
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getFrontendClasses($entityTypeCode)
    {
        $_defaultClasses = $this->_getDefaultFrontendClasses();
        if (isset($this->_entityTypeFrontendClasses[$entityTypeCode])) {
            return array_merge(
                $_defaultClasses,
                $this->_entityTypeFrontendClasses[$entityTypeCode]
            );
        }
        $_entityTypeClasses = Mage::app()->getConfig()
            ->getNode('global/eav_frontendclasses/' . $entityTypeCode);
        if ($_entityTypeClasses) {
            foreach ($_entityTypeClasses->children() as $item) {
                $this->_entityTypeFrontendClasses[$entityTypeCode][] = array(
                    'value' => (string)$item->value,
                    'label' => (string)$item->label
                );
            }
            return array_merge(
                $_defaultClasses,
                $this->_entityTypeFrontendClasses[$entityTypeCode]
            );
        }
        return $_defaultClasses;
    }

    /**
     * Retrieve attributes locked fields to edit
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getAttributeLockedFields($entityTypeCode)
    {
        if (!$entityTypeCode) {
            return array();
        }
        if (isset($this->_attributesLockedFields[$entityTypeCode])) {
            return $this->_attributesLockedFields[$entityTypeCode];
        }
        $_data = Mage::app()->getConfig()->getNode('global/eav_attributes/' . $entityTypeCode);
        if ($_data) {
            foreach ($_data->children() as $attribute) {
                $this->_attributesLockedFields[$entityTypeCode][(string)$attribute->code] =
                    array_keys($attribute->locked_fields->asArray());
            }
            return $this->_attributesLockedFields[$entityTypeCode];
        }
        return array();
    }

    /**
     * Get input types validator data
     *
     * @return array
     */
    public function getInputTypesValidatorData()
    {
        return Mage::getStoreConfig(self::XML_PATH_VALIDATOR_DATA_INPUT_TYPES);
    }
}
