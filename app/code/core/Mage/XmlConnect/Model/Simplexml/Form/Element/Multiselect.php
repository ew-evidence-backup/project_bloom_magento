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
 * Xmlconnect form multiselect element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_Multiselect
    extends Mage_XmlConnect_Model_Simplexml_Form_Element_Select
{
    /**
     * Init text element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('multiselect');
    }

    /**
     * Add value to element
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Multiselect
     */
    protected function _addValue(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $values = array();
        if (is_array($this->getEscapedValue())) {
            $values = $this->getEscapedValue();
        }

        $valuesXmlObj = $xmlObj->addCustomChild('values');
        foreach ($this->getOptions() as $option) {

            if (empty($option['value'])) {
                continue;
            }

            $selected = array();
            if (in_array($option['value'], $values)) {
                $selected = array('selected' => 1);
            }

            $valuesXmlObj->addCustomChild('item', null, array(
                'label' => $option['label'],
                'value' => $option['value']
            ) + $selected);
        }

        return $this;
    }
}
