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
 * Grid radiogroup column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Radio extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 55;
    protected $_values;

    public function getValues()
    {
        if (is_null($this->_values)) {
            $this->_values = $this->getColumn()->getData('values') ? $this->getColumn()->getData('values') : array();
        }
        return $this->_values;
    }
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $values = $this->getColumn()->getValues();
        $value  = $row->getData($this->getColumn()->getIndex());
        if (is_array($values)) {
            $checked = in_array($value, $values) ? ' checked="checked"' : '';
        }
        else {
            $checked = ($value === $this->getColumn()->getValue()) ? ' checked="checked"' : '';
        }
        return '<input type="radio" name="'.$this->getColumn()->getHtmlName().'" value="' . $row->getId() . '" class="radio"'.$checked.'/>';
    }

    /*
    public function renderHeader()
    {
        $checked = '';
        if ($filter = $this->getColumn()->getFilter()) {
            $checked = $filter->getValue() ? 'checked' : '';
        }
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" onclick="'.$this->getColumn()->getGrid()->getJsObjectName().'.checkCheckboxes(this)" class="checkbox" '.$checked.' title="'.Mage::helper('adminhtml')->__('Select All').'"/>';
    }
    */
}
