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
 * Customer address book xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Address_List extends Mage_Core_Block_Template
{
    /**
     * Render customer address list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $addressXmlObj          = Mage::getModel('xmlconnect/simplexml_element', '<address></address>');
        $customer               = Mage::getSingleton('customer/session')->getCustomer();

        $_billingAddssesId      = $customer->getDefaultBilling();
        $_shippingAddssesId     = $customer->getDefaultShipping();
        $billingAddress         = $customer->getAddressById($_billingAddssesId);
        $shippingAddress        = $customer->getAddressById($_shippingAddssesId);

        if ($billingAddress && $billingAddress->getId()) {
            $item = $addressXmlObj->addChild('item');
            $item->addAttribute('label', $this->__('Default Billing Address'));
            $item->addAttribute('default_billing', 1);
            $this->prepareAddressData($billingAddress, $item);
        }
        if ($shippingAddress && $shippingAddress->getId()) {
            $item = $addressXmlObj->addChild('item');
            $item->addAttribute('label', $this->__('Default Shipping Address'));
            $item->addAttribute('default_shipping', 1);
            $this->prepareAddressData($shippingAddress, $item);
        }
        $_additionalAddresses = $customer->getAdditionalAddresses();
        if ($_additionalAddresses) {
            foreach ($_additionalAddresses as $_address) {
                $item = $addressXmlObj->addChild('item');
                $item->addAttribute('label', $this->__('Additional Address'));
                $item->addAttribute('additional', 1);
                $this->prepareAddressData($_address, $item);
            }
        }

        return $addressXmlObj->asNiceXml();
    }

    /**
     * Collect address data to xml node
     * Remove objects from data array and escape data values
     *
     * @param Mage_Customer_Model_Address $address
     * @param Mage_XmlConnect_Model_Simplexml_Element $item
     * @return array
     */
    public function prepareAddressData(
        Mage_Customer_Model_Address $address,
        Mage_XmlConnect_Model_Simplexml_Element $item
    )
    {
        if (!$address) {
            return array();
        }

        $attributes = Mage::helper('customer/address')->getAttributes();

        $data = array('entity_id' => $address->getId());

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            if (!$attribute->getIsVisible()) {
                continue;
            }
            if ($attribute->getAttributeCode() == 'country_id') {
                $data['country'] = $address->getCountryModel()->getName();
                $data['country_id'] = $address->getCountryId();
            } else if ($attribute->getAttributeCode() == 'region') {
                $data['region'] = $address->getRegion();
            } else {
                $dataModel = Mage_Customer_Model_Attribute_Data::factory($attribute, $address);
                $value     = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE);
                if ($attribute->getFrontendInput() == 'multiline') {
                    $values = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY);
                    // explode lines
                    foreach ($values as $k => $v) {
                        $key = sprintf('%s%d', $attribute->getAttributeCode(), $k + 1);
                        $data[$key] = $v;
                    }
                }
                $data[$attribute->getAttributeCode()] = $value;
            }
        }

        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $item->addChild($key, $item->xmlentities($value));
        }
    }
}
