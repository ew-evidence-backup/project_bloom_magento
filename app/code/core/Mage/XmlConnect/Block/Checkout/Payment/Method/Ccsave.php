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
 * CC Save Payment method xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Ccsave extends Mage_Payment_Block_Form_Ccsave
{
    /**
     * Prevent any rendering
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        $method = $this->getData('method');
        if (!$method) {
            $method = Mage::getModel('payment/method_ccsave');
            $this->setData('method', $method);
        }

        return $method;
    }

    /**
     * Add cc save payment method form to payment XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $paymentItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentFormToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $paymentItemXmlObj)
    {
        $helper = Mage::helper('xmlconnect');
        $method = $this->getMethod();
        if (!$method) {
            return $paymentItemXmlObj;
        }
        $formXmlObj = $paymentItemXmlObj->addChild('form');
        $formXmlObj->addAttribute('name', 'payment_form_' . $method->getCode());
        $formXmlObj->addAttribute('method', 'post');

        $owner = $this->getInfoData('cc_owner');

        $ccTypes = $helper->getArrayAsXmlItemValues($this->getCcAvailableTypes(), $this->getInfoData('cc_type'));

        $_ccMonthArray = $this->getCcMonths();
        $ccMonths = $helper->getArrayAsXmlItemValues($_ccMonthArray, $this->getInfoData('cc_exp_month'));

        $ccYears = $helper->getArrayAsXmlItemValues($this->getCcYears(), $this->getInfoData('cc_exp_year'));

        $verification = '';
        if ($this->hasVerification()) {
            $verification =
            '<field name="payment[cc_cid]" type="text" label="'
                . $this->__('Card Verification Number') . '" required="true">
                <validators>
                    <validator relation="payment[cc_type]" type="credit_card_svn" message="'
                . $this->__('Card verification number is wrong') . '"/>
                </validators>
            </field>';
        }

        $solo = '';
        if ($this->hasSsCardType()) {
            $ssCcMonths = $helper->getArrayAsXmlItemValues(
                $_ccMonthArray, $this->getInfoData('cc_ss_start_month')
            );
            $ssCcYears = $helper->getArrayAsXmlItemValues(
                $this->getSsStartYears(), $this->getInfoData('cc_ss_start_year')
            );
            $solo = $helper->getSoloXml($ssCcMonths, $ssCcYears);
        }

        $xml = <<<EOT
    <fieldset>
        <field name="payment[cc_owner]" type="text" label="{$this->__('Name on Card')}" value="$owner" required="true" />
        <field name="payment[cc_type]" type="select" label="{$this->__('Credit Card Type')}" required="true">
            <values>
                {$ccTypes}
            </values>
            {$solo}
        </field>
        <field name="payment[cc_number]" type="text" label="{$this->__('Credit Card Number')}" required="true">
            <validators>
                <validator relation="payment[cc_type]" type="credit_card" message="{$this->__('Credit card number does not match credit card type.')}"/>
            </validators>
        </field>
        <field name="payment[cc_exp_month]" type="select" label="{$this->__('Expiration Date - Month')}" required="true">
            <values>
                {$ccMonths}
            </values>
        </field>
        <field name="payment[cc_exp_year]" type="select" label="{$this->__('Expiration Date - Year')}" required="true">
            <values>
                {$ccYears}
            </values>
        </field>
        $verification
    </fieldset>
EOT;
        $fieldsetXmlObj = Mage::getModel('xmlconnect/simplexml_element', $xml);
        $formXmlObj->appendChild($fieldsetXmlObj);
    }
}
