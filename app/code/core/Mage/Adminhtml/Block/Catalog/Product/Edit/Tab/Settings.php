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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Create product settings tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','attribute_set_id','product_type')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('catalog')->__('Create Product Settings')));

        $entityType = Mage::registry('product')->getResource()->getEntityType();

        $fieldset->addField('attribute_set_id', 'select', array(
            'label' => Mage::helper('catalog')->__('Attribute Set'),
            'title' => Mage::helper('catalog')->__('Attribute Set'),
            'name'  => 'set',
            'value' => $entityType->getDefaultAttributeSetId(),
            'values'=> Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityType->getId())
                ->load()
                ->toOptionArray()
        ));

        $fieldset->addField('product_type', 'select', array(
            'label' => Mage::helper('catalog')->__('Product Type'),
            'title' => Mage::helper('catalog')->__('Product Type'),
            'name'  => 'type',
            'value' => '',
            'values'=> Mage::getModel('catalog/product_type')->getOptionArray()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'set'       => '{{attribute_set}}',
            'type'      => '{{type}}'
        ));
    }
}
