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
 * @package     Mage_Widget
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Widget Instance template chooser
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Template
    extends Mage_Adminhtml_Block_Widget
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getWidgetTemplates()) {
            $html = '<p class="nm"><small>' . Mage::helper('widget')->__('Please Select Block Reference First') . '</small></p>';
        } elseif (count($this->getWidgetTemplates()) == 1) {
            $widgetTemplate = current($this->getWidgetTemplates());
            $html = '<input type="hidden" name="template" value="' . $widgetTemplate['value'] . '" />';
            $html .= $widgetTemplate['label'];
        } else {
            $html = $this->getLayout()->createBlock('core/html_select')
                ->setName('template')
                ->setClass('select')
                ->setOptions($this->getWidgetTemplates())
                ->setValue($this->getSelected())->toHtml();
        }
        return parent::_toHtml().$html;
    }
}
