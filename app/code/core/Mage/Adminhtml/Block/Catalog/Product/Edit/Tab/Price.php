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
 * Adminhtml product edit price block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $product = Mage::registry('product');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('tiered_price', array('legend'=>Mage::helper('catalog')->__('Tier Pricing')));

        $fieldset->addField('default_price', 'label', array(
                'label'=> Mage::helper('catalog')->__('Default Price'),
                'title'=> Mage::helper('catalog')->__('Default Price'),
                'name'=>'default_price',
                'bold'=>true,
                'value'=>$product->getPrice()
        ));

        $fieldset->addField('tier_price', 'text', array(
                'name'=>'tier_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('tier_price')
        ));

        $form->getElement('tier_price')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
        );

        $this->setForm($form);
    }
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price END
