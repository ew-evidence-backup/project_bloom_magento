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
 * Eav Form Element Model
 *
 * @method Mage_Eav_Model_Resource_Form_Element _getResource()
 * @method Mage_Eav_Model_Resource_Form_Element getResource()
 * @method int getTypeId()
 * @method Mage_Eav_Model_Form_Element setTypeId(int $value)
 * @method int getFieldsetId()
 * @method Mage_Eav_Model_Form_Element setFieldsetId(int $value)
 * @method int getAttributeId()
 * @method Mage_Eav_Model_Form_Element setAttributeId(int $value)
 * @method int getSortOrder()
 * @method Mage_Eav_Model_Form_Element setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Form_Element extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_element';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('eav/form_element');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_Eav_Model_Mysql4_Form_Element
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve resource collection instance wrapper
     *
     * @return Mage_Eav_Model_Mysql4_Form_Element_Collection
     */
    public function getCollection()
    {
        return parent::getCollection();
    }

    /**
     * Validate data before save data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Eav_Model_Form_Element
     */
    protected function _beforeSave()
    {
        if (!$this->getTypeId()) {
            Mage::throwException(Mage::helper('eav')->__('Invalid form type.'));
        }
        if (!$this->getAttributeId()) {
            Mage::throwException(Mage::helper('eav')->__('Invalid EAV attribute.'));
        }

        return parent::_beforeSave();
    }

    /**
     * Retrieve EAV Attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute()
    {
        if (!$this->hasData('attribute')) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute($this->getEntityTypeId(), $this->getAttributeId());
            $this->setData('attribute', $attribute);
        }
        return $this->_getData('attribute');
    }
}
