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
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'text', 'label' => Mage::helper('eav')->__('Text Field')),
            array('value' => 'textarea', 'label' => Mage::helper('eav')->__('Text Area')),
            array('value' => 'date', 'label' => Mage::helper('eav')->__('Date')),
            array('value' => 'boolean', 'label' => Mage::helper('eav')->__('Yes/No')),
            array('value' => 'multiselect', 'label' => Mage::helper('eav')->__('Multiple Select')),
            array('value' => 'select', 'label' => Mage::helper('eav')->__('Dropdown'))
        );
    }
}
