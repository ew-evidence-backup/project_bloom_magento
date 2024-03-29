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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * HTML select element block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Html_Date extends Mage_Core_Block_Template
{

    protected function _toHtml()
    {
        $displayFormat = Varien_Date::convertZendToStrFtime($this->getFormat(), true, (bool)$this->getTime());

        $html  = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';

        $html .= '<img src="' . $this->getImage() . '" alt="' . $this->helper('core')->__('Select Date') . '" class="v-middle" ';
        $html .= 'title="' . $this->helper('core')->__('Select Date') . '" id="' . $this->getId() . '_trig" />';

        $html .=
        '<script type="text/javascript">
        //<![CDATA[
            var calendarSetupObject = {
                inputField  : "' . $this->getId() . '",
                ifFormat    : "' . $displayFormat . '",
                showsTime   : "' . ($this->getTime() ? 'true' : 'false') . '",
                button      : "' . $this->getId() . '_trig",
                align       : "Bl",
                singleClick : true
            }';

        $calendarYearsRange = $this->getYearsRange();
        if ($calendarYearsRange) {
            $html .= '
                calendarSetupObject.range = ' . $calendarYearsRange . '
                ';
        }

        $html .= '
            Calendar.setup(calendarSetupObject);
        //]]>
        </script>';


        return $html;
    }

    public function getEscapedValue($index=null) {

        if($this->getFormat() && $this->getValue()) {
            return strftime($this->getFormat(), strtotime($this->getValue()));
        }

        return htmlspecialchars($this->getValue());
    }

    public function getHtml()
    {
        return $this->toHtml();
    }

}
