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
 * Adminhtml Grid Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Longtext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render contents as a long text
     *
     * Text will be truncated as specified in string_limit, truncate or 250 by default
     * Also it can be html-escaped and nl2br()
     *
     * @param Varien_Object $row
     * @return unknown
     */
    public function render(Varien_Object $row)
    {
        $truncateLength = 250;
        // stringLength() is for legacy purposes
        if ($this->getColumn()->getStringLimit()) {
            $truncateLength = $this->getColumn()->getStringLimit();
        }
        if ($this->getColumn()->getTruncate()) {
            $truncateLength = $this->getColumn()->getTruncate();
        }
        $text = Mage::helper('core/string')->truncate(parent::_getValue($row), $truncateLength);
        if ($this->getColumn()->getEscape()) {
            $text = $this->htmlEscape($text);
        }
        if ($this->getColumn()->getNl2br()) {
            $text = nl2br($text);
        }
        return $text;
    }
}
