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

/**
 * Customer store attribute source
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Source_Store extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('core/store_collection');
            if ('store_id' == $this->getAttribute()->getAttributeCode()) {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm();
            if ('created_in' == $this->getAttribute()->getAttributeCode()) {
                array_unshift($this->_options, array('value' => '0', 'label' => Mage::helper('customer')->__('Admin')));
            }
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        if(!$value)$value ='0';
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        if (!$this->_options) {
            $collection = Mage::getResourceModel('core/store_collection');
            if ('store_id' == $this->getAttribute()->getAttributeCode()) {
                $collection->setWithoutDefaultFilter();
            }
            $this->_options = $collection->load()->toOptionArray();
            if ('created_in' == $this->getAttribute()->getAttributeCode()) {
                array_unshift($this->_options, array('value' => '0', 'label' => Mage::helper('customer')->__('Admin')));
            }
        }

        if ($isMultiple) {
            $values = array();
            foreach ($value as $val) {
                $values[] = $this->_options[$val];
            }
            return $values;
        }
        else {
            return $this->_options[$value];
        }
        return false;
    }
}
