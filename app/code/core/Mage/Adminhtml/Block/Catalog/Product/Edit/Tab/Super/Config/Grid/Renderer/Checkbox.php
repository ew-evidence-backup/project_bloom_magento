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
 * Adminhtml catalog super product link grid checkbox renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $result = parent::render($row);
        return $result.'<input type="hidden" class="value-json" value="'.htmlspecialchars($this->getAttributesJson($row)).'" />';
    }

    public function getAttributesJson(Varien_Object $row)
    {
        if(!$this->getColumn()->getAttributes()) {
            return '[]';
        }

        $result = array();
        foreach($this->getColumn()->getAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            if($productAttribute->getSourceModel()) {
                $label = $productAttribute->getSource()->getOptionText($row->getData($productAttribute->getAttributeCode()));
            } else {
                $label = $row->getData($productAttribute->getAttributeCode());
            }
            $item = array();
            $item['label']        = $label;
            $item['attribute_id'] = $productAttribute->getId();
            $item['value_index']  = $row->getData($productAttribute->getAttributeCode());
            $result[] = $item;
        }

        return Mage::helper('core')->jsonEncode($result);
    }
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Checkbox END
