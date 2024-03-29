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
 * Catalog Category Attribute Default and Available Sort By Backend Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Attribute_Backend_Sortby
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Validate process
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $postDataConfig = ($object->getData('use_post_data_config'))? $object->getData('use_post_data_config') : array();
        
        $isUseConfig = false;
        if ($postDataConfig) {
            $isUseConfig = in_array($attributeCode, $postDataConfig);
        }
        
        if ($this->getAttribute()->getIsRequired()) {
            $attributeValue = $object->getData($attributeCode);
            if ($this->getAttribute()->isValueEmpty($attributeValue)) {
                if (is_array($attributeValue) && count($attributeValue)>0) {
                } else {
                    if(!$isUseConfig) {
                        return false;
                    }
                }
            }
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be unique.', $label));
            }
        }
        
        if ($attributeCode == 'default_sort_by') {
            if ($available = $object->getData('available_sort_by')) {
                if (!is_array($available)) {
                    $available = explode(',', $available);
                }
                $data = (!in_array('default_sort_by', $postDataConfig))? $object->getData($attributeCode):
                       Mage::getStoreConfig("catalog/frontend/default_sort_by");
                if (!in_array($data, $available)) {
                    Mage::throwException(Mage::helper('eav')->__('Default Product Listing Sort by not exists on Available Product Listing Sort By'));
                }
            } else {
                if (!in_array('available_sort_by', $postDataConfig)) {
                    Mage::throwException(Mage::helper('eav')->__('Default Product Listing Sort by not exists on Available Product Listing Sort By'));
                }
            }
        }

        return true;        
    }

    /**
     * Before Attribute Save Process
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Category_Attribute_Backend_Sortby
     */
    public function beforeSave($object) {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = array();
            }
            $object->setData($attributeCode, join(',', $data));
        }
        if (is_null($object->getData($attributeCode))) {
            $object->setData($attributeCode, false);
        }
        return $this;
    }

    public function afterLoad($object) {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if ($data) {
                $object->setData($attributeCode, explode(',', $data));
            }
        }
        return $this;
    }
}
