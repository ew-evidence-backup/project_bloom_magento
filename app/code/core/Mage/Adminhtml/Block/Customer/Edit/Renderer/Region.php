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
 * Customer address region field renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Region extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Output the region element and javasctipt that makes it dependent from country element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        }
        else {
            return $element->getDefaultHtml();
        }

        $regionId = $element->getForm()->getElement('region_id')->getValue();

        $html = '<tr>';
        $element->setClass('input-text');
        $html.= '<td class="label">'.$element->getLabelHtml().'</td><td class="value">';
        $html.= $element->getElementHtml();

        $selectName = str_replace('region', 'region_id', $element->getName());
        $selectId   = $element->getHtmlId().'_id';
        $html.= '<select id="'.$selectId.'" name="'.$selectName.'" class="select required-entry" style="display:none">';
        $html.= '<option value="">'.Mage::helper('customer')->__('Please select').'</option>';
        $html.= '</select>';

        $html.= '<script type="text/javascript">'."\n";
        $html.= 'new regionUpdater("'.$country->getHtmlId().'", "'.$element->getHtmlId().'", "'.$selectId.'", '.$this->helper('directory')->getRegionJson().');'."\n";
        $html.= '</script>'."\n";

        $html.= '</td></tr>'."\n";
        return $html;
    }
}
