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
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select extends
    Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/options/type/select.phtml');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_select_row_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Add New Row'),
                    'class' => 'add add-select-row',
                    'id'    => 'add_select_row_button_{{option_id}}'
                ))
        );

        $this->setChild('delete_select_row_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete Row'),
                    'class' => 'delete delete-select-row icon-btn',
                    'id'    => 'delete_select_row_button'
                ))
        );

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_select_row_button');
    }

    public function getPriceTypeSelectHtml()
    {
        $this->getChild('option_price_type')
            ->setData('id', 'product_option_{{id}}_select_{{select_id}}_price_type')
            ->setName('product[options][{{id}}][values][{{select_id}}][price_type]');

        return parent::getPriceTypeSelectHtml();
    }
}
