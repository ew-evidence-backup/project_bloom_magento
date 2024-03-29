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
 * Customer order Customer balance totals xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Totals_Giftcards
    extends Enterprise_GiftCardAccount_Block_Sales_Order_Giftcards
{
    /**
     * Add order total rendered to XML object
     *
     * @param $totalsXml Mage_XmlConnect_Model_Simplexml_Element
     * @return null
     */
    public function addToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $totalsXml)
    {
        $cards  = $this->getGiftCards();
        if ($cards) {
            foreach ($cards as $card) {
                $label = Mage::helper('enterprise_giftcardaccount')->__('Gift Card (%s)', $card->getCode());
                $totalsXml->addCustomChild($this->getTotal()->getCode(), '-' . $this->_formatPrice($card->getAmount()),
                    array('label' => $label)
                );
            }
        } else {
            $cardsAmount = $this->getSource()->getGiftCardsAmount();
            if ($cardsAmount > 0) {
                $totalsXml->addCustomChild($this->getTotal()->getCode(), '-' . $this->_formatPrice($cardsAmount),
                    array('label' => Mage::helper('enterprise_giftcardaccount')->__('Gift Card'))
                );
            }
        }
    }

    /**
     * Format price using order currency
     *
     * @param   float $amount
     * @return  string
     */
    protected function _formatPrice($amount)
    {
        return Mage::helper('xmlconnect/customer_order')->formatPrice($this, $amount);
    }
}
