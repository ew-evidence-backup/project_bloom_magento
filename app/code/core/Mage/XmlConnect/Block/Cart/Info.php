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
 * Shopping cart summary information xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Info extends Mage_XmlConnect_Block_Cart
{
    /**
     * Render cart summary xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $this->getQuote();
        /** @var $xmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObject  = Mage::getModel('xmlconnect/simplexml_element', '<cart></cart>');

        $xmlObject->addChild('is_virtual', (int)$this->helper('checkout/cart')->getIsVirtualQuote());

        $xmlObject->addChild('summary_qty', (int)$this->helper('checkout/cart')->getSummaryCount());

        $xmlObject->addChild('virtual_qty', (int)$quote->getItemVirtualQty());

        if (strlen($quote->getCouponCode())) {
            $xmlObject->addChild('has_coupon_code', 1);
        }

        $totalsXml = $this->getChildHtml('totals');
        if ($totalsXml) {
            /** @var $totalsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
            $totalsXmlObj = Mage::getModel('xmlconnect/simplexml_element', $totalsXml);
            $xmlObject->appendChild($totalsXmlObj);
        }
        return $xmlObject->asNiceXml();
    }
}
