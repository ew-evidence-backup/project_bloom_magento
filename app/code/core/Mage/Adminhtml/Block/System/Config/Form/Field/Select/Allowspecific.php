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
 * System congifuration shipping methods allow all countries selec
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Select_Allowspecific extends Varien_Data_Form_Element_Select
{

    public function getAfterElementHtml()
    {
        $javaScript = "
            <script type=\"text/javascript\">
                Event.observe('{$this->getHtmlId()}', 'change', function(){
                    specific=$('{$this->getHtmlId()}').value;
                    $('{$this->_getSpecificCountryElementId()}').disabled = (!specific || specific!=1);
                });
            </script>";
        return $javaScript . parent::getAfterElementHtml();
    }

    public function getHtml()
    {
        if(!$this->getValue() || $this->getValue()!=1) {
            $this->getForm()->getElement($this->_getSpecificCountryElementId())->setDisabled('disabled');
        }
        return parent::getHtml();
    }

    protected function _getSpecificCountryElementId()
    {
        return substr($this->getId(), 0, strrpos($this->getId(), 'allowspecific')) . 'specificcountry';
    }

}
