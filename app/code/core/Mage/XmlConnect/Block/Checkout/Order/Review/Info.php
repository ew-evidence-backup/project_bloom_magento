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
 * One page checkout order info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Order_Review_Info extends Mage_Checkout_Block_Onepage_Review_Info
{
    /**
     * Render order review items
     *
     * @return string
     */
    protected function _toHtml()
    {
        $itemsXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<products></products>');
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $type = $this->_getItemType($item);
            $renderer = $this->getItemRenderer($type)->setItem($item);

            /**
             * General information
             */
            $itemXml = $itemsXmlObj->addChild('item');
            $itemXml->addChild('entity_id', $item->getProduct()->getId());
            $itemXml->addChild('entity_type', $type);
            $itemXml->addChild('item_id', $item->getId());
            $itemXml->addChild('name', $itemsXmlObj->xmlentities($renderer->getProductName()));
            $itemXml->addChild('qty', $renderer->getQty());
            $icon = $renderer->getProductThumbnail()->resize(
                Mage::helper('xmlconnect/image')->getImageSizeForContent('product_small')
            );

            $iconXml = $itemXml->addChild('icon', $icon);

            $file = Mage::helper('xmlconnect')->urlToPath($icon);
            $iconXml->addAttribute('modification_time', filemtime($file));

            /**
             * Price
             */
            $exclPrice = $inclPrice = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $typeOfDisplay = Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales');
                if ($typeOfDisplay && $item->getWeeeTaxAppliedAmount()) {
                    $exclPrice = $item->getCalculationPrice() + $item->getWeeeTaxAppliedAmount()
                        + $item->getWeeeTaxDisposition();
                } else {
                    $exclPrice = $item->getCalculationPrice();
                }
            }

            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $_incl = $this->helper('checkout')->getPriceInclTax($item);
                $typeOfDisplay = Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales');
                if ($typeOfDisplay && $item->getWeeeTaxAppliedAmount()) {
                    $inclPrice = $_incl + $item->getWeeeTaxAppliedAmount();
                } else {
                    $inclPrice = $_incl - $item->getWeeeTaxDisposition();
                }
            }

            $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $priceXmlObj = $itemXml->addChild('price');
            $formattedPriceXmlObj = $itemXml->addChild('formated_price');

            if ($this->helper('tax')->displayCartBothPrices()) {
                $priceXmlObj->addAttribute('excluding_tax', $exclPrice);
                $priceXmlObj->addAttribute('including_tax', $inclPrice);

                $formattedPriceXmlObj->addAttribute('excluding_tax', $formattedExclPrice);
                $formattedPriceXmlObj->addAttribute('including_tax', $formattedInclPrice);
            } else {
                if ($this->helper('tax')->displayCartPriceExclTax()) {
                    $priceXmlObj->addAttribute('regular', $exclPrice);
                    $formattedPriceXmlObj->addAttribute('regular', $formattedExclPrice);
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                    $priceXmlObj->addAttribute('regular', $inclPrice);
                    $formattedPriceXmlObj->addAttribute('regular', $formattedInclPrice);
                }
            }

            /**
             * Subtotal
             */
            $exclPrice = $inclPrice = 0.00;
            if ($this->helper('tax')->displayCartPriceExclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $typeOfDisplay = Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales');
                if ($typeOfDisplay && $item->getWeeeTaxAppliedAmount()) {
                    $exclPrice = $item->getRowTotal() + $item->getWeeeTaxAppliedRowAmount()
                        + $item->getWeeeTaxRowDisposition();
                } else {
                    $exclPrice = $item->getRowTotal();
                }
            }
            if ($this->helper('tax')->displayCartPriceInclTax() || $this->helper('tax')->displayCartBothPrices()) {
                $_incl = $this->helper('checkout')->getSubtotalInclTax($item);
                if (Mage::helper('weee')->typeOfDisplay($item, array(0, 1, 4), 'sales')
                    && $item->getWeeeTaxAppliedAmount()
                ) {
                    $inclPrice = $_incl + $item->getWeeeTaxAppliedRowAmount();
                } else {
                    $inclPrice = $_incl - $item->getWeeeTaxRowDisposition();
                }
            }

            $exclPrice = Mage::helper('xmlconnect')->formatPriceForXml($exclPrice);
            $formattedExclPrice = $quote->getStore()->formatPrice($exclPrice, false);

            $inclPrice = Mage::helper('xmlconnect')->formatPriceForXml($inclPrice);
            $formattedInclPrice = $quote->getStore()->formatPrice($inclPrice, false);

            $subtotalPriceXmlObj = $itemXml->addChild('subtotal');
            $subtotalFormattedPriceXmlObj = $itemXml->addChild('formated_subtotal');

            if ($this->helper('tax')->displayCartBothPrices()) {
                $subtotalPriceXmlObj->addAttribute('excluding_tax', $exclPrice);
                $subtotalPriceXmlObj->addAttribute('including_tax', $inclPrice);

                $subtotalFormattedPriceXmlObj->addAttribute('excluding_tax', $formattedExclPrice);
                $subtotalFormattedPriceXmlObj->addAttribute('including_tax', $formattedInclPrice);
            } else {
                if ($this->helper('tax')->displayCartPriceExclTax()) {
                    $subtotalPriceXmlObj->addAttribute('regular', $exclPrice);
                    $subtotalFormattedPriceXmlObj->addAttribute('regular', $formattedExclPrice);
                }
                if ($this->helper('tax')->displayCartPriceInclTax()) {
                    $subtotalPriceXmlObj->addAttribute('regular', $inclPrice);
                    $subtotalFormattedPriceXmlObj->addAttribute('regular', $formattedInclPrice);
                }
            }

            /**
             * Options list
             */
            if ($_options = $renderer->getOptionList()) {
                $itemOptionsXml = $itemXml->addChild('options');
                foreach ($_options as $_option) {
                    $_formattedOptionValue = $renderer->getFormatedOptionValue($_option);
                    $optionXml = $itemOptionsXml->addChild('option');
                    $labelValue = $itemsXmlObj->xmlentities($_option['label']);
                    $optionXml->addAttribute('label', $labelValue);
                    $textValue = $itemsXmlObj->xmlentities(
                        strip_tags($_formattedOptionValue['value'])
                    );
                    $optionXml->addAttribute('text', $textValue);
                }
            }
        }

        return $itemsXmlObj->asNiceXml();
    }
}
