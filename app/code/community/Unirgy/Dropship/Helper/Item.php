<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_Dropship
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

class Unirgy_Dropship_Helper_Item extends Mage_Core_Helper_Abstract
{
    const STICKED_UDROPSHIP_VENDOR_OPTION = 'sticked_udropship_vendor';
    const UDROPSHIP_VENDOR_OPTION = 'udropship_vendor';
    public function getStickedVendorIdOption($item)
    {
        return $this->_getItemOption($item, 'sticked_udropship_vendor');
    }
    public function saveStickedVendorIdOption($item, $vId)
    {
        $this->_saveItemOption($item, Unirgy_Dropship_Helper_Item::STICKED_UDROPSHIP_VENDOR_OPTION, $vId, false);
        return $this;
    }
    public function deleteStickedVendorIdOption($item)
    {
        $this->deleteItemOption($item, Unirgy_Dropship_Helper_Item::STICKED_UDROPSHIP_VENDOR_OPTION);
        return $this;
    }
    public function getVendorIdOption($item)
    {
        return $this->_getItemOption($item, 'udropship_vendor');
    }
    public function deleteVendorIdOption($item)
    {
        $this->deleteItemOption($item, 'udropship_vendor');
        $addOptions = $this->getAdditionalOptions($item);
        if (!empty($addOptions) && is_string($addOptions)) {
            $addOptions = unserialize($addOptions);
        }
        if (!is_array($addOptions)) {
            $addOptions = array();
        }
        foreach ($addOptions as $idx => $option) {
            if (!empty($option['udropship_vendor'])) {
                $vendorOptionIdx = $idx;
                break;
            }
        }
        unset($addOptions[$vendorOptionIdx]);
        $this->saveAdditionalOptions($item, $addOptions);
        return $this;
    }
    public function setVendorIdOption($item, $value, $visible=false)
    {
        $this->saveItemOption($item, 'udropship_vendor', $value, false);
        if ($visible) {
            $addOptions = $this->getAdditionalOptions($item);
            if (!empty($addOptions) && is_string($addOptions)) {
                $addOptions = unserialize($addOptions);
            }
            if (!is_array($addOptions)) {
                $addOptions = array();
            }
            foreach ($addOptions as $idx => $option) {
                if (!empty($option['udropship_vendor'])) {
                    $vendorOptionIdx = $idx;
                    break;
                }
            }
            $vendorOption['label'] = Mage::helper('udropship')->__('Vendor');
            $vendorOption['value'] = Mage::helper('udropship')->getVendor($value)->getVendorName();
            if (isset($vendorOptionIdx)) {
                $addOptions[$vendorOptionIdx] = $vendorOption;
            } else {
                $addOptions[] = $vendorOption;
            }
            $this->saveAdditionalOptions($item, $addOptions);
        }
        return $this;
    }
    public function getAdditionalOptions($item)
    {
        return $this->_getItemOption($item, 'additional_options');
    }
    public function getItemOption($item, $code)
    {
        return $this->_getItemOption($item, $code);
    }
    protected function _getItemOption($item, $code)
    {
        $optValue = null;
        if ($item instanceof Mage_Catalog_Model_Product
            && $item->getCustomOption($code)
        ) {
            $optValue = $item->getCustomOption($code)->getValue();
        } elseif ($item instanceof Mage_Sales_Model_Quote_Item
            && $item->getOptionByCode($code)
        ) {
            $optValue = $item->getOptionByCode($code)->getValue();
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item && $item->getQuoteItem()
            && $item->getQuoteItem()->getOptionByCode($code)
        ) {
            $optValue = $item->getQuoteItem()->getOptionByCode($code)->getValue();
        } elseif ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
            if (isset($options[$code])) {
                $optValue = $options[$code];
            }
        } elseif ($item instanceof Varien_Object && $item->getOrderItem()) {
            $options = $item->getOrderItem()->getProductOptions();
            if (isset($options[$code])) {
                $optValue = $options[$code];
            }
        }
        return $optValue;
    }
    public function saveAdditionalOptions($item, $options)
    {
        return $this->_saveItemOption($item, 'additional_options', $options, true);
    }
    public function saveItemOption($item, $code, $value, $serialize)
    {
        return $this->_saveItemOption($item, $code, $value, $serialize);
    }
    protected function _saveItemOption($item, $code, $value, $serialize)
    {
        if ($item instanceof Mage_Catalog_Model_Product) {
            if ($item->getCustomOption($code)) {
                $item->getCustomOption($code)->setValue($serialize ? serialize($value) : $value);
            } else {
                $item->addCustomOption($code, $serialize ? serialize($value) : $value);
            }
        } elseif ($item instanceof Mage_Sales_Model_Quote_Item) {
            $optionsByCode = $item->getOptionsByCode();
            if (isset($optionsByCode[$code])) {
                $optionsByCode[$code]->isDeleted(false);
                $optionsByCode[$code]->setValue($serialize ? serialize($value) : $value);
            } else {
                $item->addOption(array(
                    'product' => $item->getProduct(),
                    'product_id' => $item->getProduct()->getId(),
                    'code' => $code,
                    'value' => $serialize ? serialize($value) : $value
                ));
            }
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item && $item->getQuoteItem()) {
            if ($item->getQuoteItem()->getOptionByCode($code)) {
                $item->getQuoteItem()->getOptionByCode($code)->setValue($serialize ? serialize($value) : $value);
            } else {
                $item->getQuoteItem()->addOption(array(
                    'product' => $item->getQuoteItem()->getProduct(),
                    'product_id' => $item->getQuoteItem()->getProduct()->getId(),
                    'code' => $code,
                    'value' => $serialize ? serialize($value) : $value
                ));
            }
        } elseif ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
            $options[$code] = $value;
            $item->setProductOptions($options);
        } elseif ($item instanceof Varien_Object && $item->getOrderItem()) {
            $options = $item->getOrderItem()->getProductOptions();
            $options[$code] = $value;
            $item->getOrderItem()->setProductOptions($options);
        }
        return $value;
    }
    public function deleteItemOption($item, $code)
    {
        return $this->_deleteItemOption($item, $code);
    }
    protected function _deleteItemOption($item, $code)
    {
        if ($item instanceof Mage_Catalog_Model_Product) {
            $customOptions = $item->getCustomOptions();
            unset($customOptions[$code]);
            $item->setCustomOptions($customOptions);
        } elseif ($item instanceof Mage_Sales_Model_Quote_Item) {
            $item->removeOption($code);
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item && $item->getQuoteItem()) {
            $item->getQuoteItem()->removeOption($code);
        } elseif ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
            unset($options[$code]);
            $item->setProductOptions($options);
        } elseif ($item instanceof Varien_Object && $item->getOrderItem()) {
            $options = $item->getOrderItem()->getProductOptions();
            unset($options[$code]);
            $item->getOrderItem()->setProductOptions($options);
        }
        return $this;
    }

    public function compareQuoteItems($item1, $item2)
    {
        if ($item1->getProductId() != $item2->getProductId()) {
            return false;
        }
        foreach ($item1->getOptions() as $option) {
            if ($option->isDeleted() || in_array($option->getCode(), array('info_buyRequest'))) {
                continue;
            }
            if ($item2Option = $item2->getOptionByCode($option->getCode())) {
                $item2OptionValue = $item2Option->getValue();
                $optionValue     = $option->getValue();

                // dispose of some options params, that can cramp comparing of arrays
                if (is_string($item2OptionValue) && is_string($optionValue)) {
                    $_itemOptionValue = @unserialize($item2OptionValue);
                    $_optionValue     = @unserialize($optionValue);
                    if (is_array($_itemOptionValue) && is_array($_optionValue)) {
                        $item2OptionValue = $_itemOptionValue;
                        $optionValue     = $_optionValue;
                        // looks like it does not break bundle selection qty
                        unset($item2OptionValue['qty'], $item2OptionValue['uenc'], $optionValue['qty'], $optionValue['uenc']);
                    }
                }

                if ($item2OptionValue != $optionValue) {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        return true;
    }

    public function createClonedQuoteItem($item, $qty)
    {
        $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($item->getProductId());
        if (!$product->getId()) {
            return false;
        }

        $info = $this->getItemOption($item, 'info_buyRequest');
        $info = new Varien_Object(unserialize($info));
        $info->setQty($qty);

        $item = $item->getQuote()->addProduct($product, $info);
        return $item;
    }

    public function attachOrderItemPoInfo($order)
    {
        if (Mage::helper('udropship')->isModuleActive('udpo')) {
            $statuses = Mage::getSingleton('udpo/source')->setPath('po_statuses')->toOptionHash();
        } else {
            $statuses = Mage::getSingleton('udropship/source')->setPath('shipment_statuses')->toOptionHash();
        }
        $poInfo = Mage::getResourceSingleton('udropship/helper')->getOrderItemPoInfo($order);
        $vendors = Mage::getSingleton('udropship/source')->getVendors(true);
        foreach ($poInfo as $poi) {
            $optKey = 'udropship_poinfo';
            $optVal = $poi['item_id'].'-'.$poi['increment_id'];
            $item = $order->getItemById($poi['item_id']);
            $addOptions = $this->getAdditionalOptions($item);
            if (!empty($addOptions) && is_string($addOptions)) {
                $addOptions = unserialize($addOptions);
            }
            if (!is_array($addOptions)) {
                $addOptions = array();
            }
            foreach ($addOptions as $idx => $option) {
                if (@$option[$optKey] == $optVal) {
                    $vendorOptionIdx = $idx;
                    break;
                }
            }
            $vendorOption['label'] = Mage::helper('udropship')->__('PO #%s Vendor', $poi['increment_id']);
            //$vendorOption['value'] = Mage::helper('udropship')->__('%s (qty: x%s) [status: %s]', @$vendors[$poi['udropship_vendor']], 1*$poi['qty'], @$statuses[$poi['udropship_status']]);
            $vendorOption['value'] = Mage::helper('udropship')->__('%s (qty: %s)', @$vendors[$poi['udropship_vendor']], 1*$poi['qty']);
            if (isset($vendorOptionIdx)) {
                $addOptions[$vendorOptionIdx] = $vendorOption;
            } else {
                $addOptions[] = $vendorOption;
            }
            $this->saveAdditionalOptions($item, $addOptions);
        }
    }
}