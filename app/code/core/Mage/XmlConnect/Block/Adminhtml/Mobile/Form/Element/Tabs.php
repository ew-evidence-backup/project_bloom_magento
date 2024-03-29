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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * XmlConnect tabs form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Tabs
    extends Varien_Data_Form_Element_Text
{
    /**
     * Generate application tabs html
     *
     * @return string
     */
    public function getHtml()
    {
        if ((bool)Mage::getSingleton('adminhtml/session')->getNewApplication()) {
            return '';
        }

        $blockClassName = Mage::getConfig()->getBlockClassName('adminhtml/template');
        $block = new $blockClassName;
        $device = Mage::helper('xmlconnect')->getDeviceType();
        if (array_key_exists($device, Mage::helper('xmlconnect')->getSupportedDevices())) {
            $template = 'xmlconnect/form/element/app_tabs_' . strtolower($device) . '.phtml';
        } else {
            Mage::throwException(
                $this->__('Device doesn\'t recognized. Unable to load a template.')
            );
        }

        $block->setTemplate($template);
        $tabs = Mage::getModel('xmlconnect/tabs', $this->getValue());
        $block->setTabs($tabs);
        $block->setName($this->getName());
        return $block->toHtml();
    }
}
