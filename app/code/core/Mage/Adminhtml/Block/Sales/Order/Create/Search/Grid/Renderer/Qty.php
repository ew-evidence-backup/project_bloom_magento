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
 * Renderer for Qty field in sales create new order search grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Qty
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    /**
     * Returns whether this qty field must be inactive
     *
     * @param   Varien_Object $row
     * @return  bool
     */
    protected function _isInactive($row)
    {
        return $row->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE;
    }

    /**
     * Render product qty field
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        // Prepare values
        $isInactive = $this->_isInactive($row);

        if ($isInactive) {
            $qty = '';
        } else {
            $qty = $row->getData($this->getColumn()->getIndex());
            $qty *= 1;
            if (!$qty) {
                $qty = '';
            }
        }

        // Compose html
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $qty . '" ';
        if ($isInactive) {
            $html .= 'disabled="disabled" ';
        }
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . ($isInactive ? ' input-inactive' : '') . '" />';
        return $html;
    }
}
