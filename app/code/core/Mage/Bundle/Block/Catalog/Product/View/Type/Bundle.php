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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Catalog bundle product info block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle extends Mage_Catalog_Block_Product_View_Abstract
{
    protected $_optionRenderers = array();
    protected $_options         = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    public function getOptions()
    {
        if (!$this->_options) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance(true);
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection, false, false);
        }

        return $this->_options;
    }

    public function hasOptions()
    {
        $this->getOptions();
        if (empty($this->_options) || !$this->getProduct()->isSalable()) {
            return false;
        }
        return true;
    }

    public function getJsonConfig()
    {
        Mage::app()->getLocale()->getJsPriceFormat();
        $optionsArray = $this->getOptions();
        $options      = array();
        $selected     = array();
        $currentProduct = $this->getProduct();
        $coreHelper   = Mage::helper('core');
        $bundlePriceModel = Mage::getModel('bundle/product_price');

        if ($preconfiguredFlag = $currentProduct->hasPreconfiguredValues()) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        foreach ($optionsArray as $_option) {
            if (!$_option->getSelections()) {
                continue;
            }

            $optionId = $_option->getId();
            $option = array (
                'selections' => array(),
                'title'      => $_option->getTitle(),
                'isMulti'    => in_array($_option->getType(), array('multi', 'checkbox'))
            );

            $selectionCount = count($_option->getSelections());

            foreach ($_option->getSelections() as $_selection) {
                $selectionId = $_selection->getSelectionId();
                $_qty = !($_selection->getSelectionQty()*1) ? '1' : $_selection->getSelectionQty()*1;
                // recalculate currency
                $tierPrices = $_selection->getTierPrice();
                foreach ($tierPrices as &$tierPriceInfo) {
                    $tierPriceInfo['price'] = $coreHelper->currency($tierPriceInfo['price'], false, false);
                }
                unset($tierPriceInfo); // break the reference with the last element

                $taxPercent = 0;
                $taxClassId = $_selection->getTaxClassId();
                if ($taxClassId) {
                    $request = Mage::getSingleton('tax/calculation')->getRateRequest();
                    $taxPercent = Mage::getSingleton('tax/calculation')->getRate(
                        $request->setProductClassId($taxClassId)
                    );
                }

                $itemPrice = $bundlePriceModel->getSelectionFinalTotalPrice($currentProduct, $_selection,
                        $currentProduct->getQty(), $_selection->getQty());

                $canApplyMAP = false;

                /* @var $taxHelper Mage_Tax_Helper_Data */
                $taxHelper = Mage::helper('tax');

                $_priceInclTax = $taxHelper->getPrice($_selection, $itemPrice, true);
                $_priceExclTax = $taxHelper->getPrice($_selection, $itemPrice);

                if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $_priceInclTax = $taxHelper->getPrice($currentProduct, $itemPrice, true);
                    $_priceExclTax = $taxHelper->getPrice($currentProduct, $itemPrice);
                }

                $selection = array (
                    'qty'       => $_qty,
                    'customQty' => $_selection->getSelectionCanChangeQty(),
                    'price'     => $coreHelper->currency($_selection->getFinalPrice(), false, false),
                    'priceInclTax'  => $coreHelper->currency($_priceInclTax, false, false),
                    'priceExclTax'  => $coreHelper->currency($_priceExclTax, false, false),
                    'priceValue' => $coreHelper->currency($_selection->getSelectionPriceValue(), false, false),
                    'priceType' => $_selection->getSelectionPriceType(),
                    'tierPrice' => $tierPrices,
                    'name'      => $_selection->getName(),
                    'plusDisposition' => 0,
                    'minusDisposition' => 0,
                    'canApplyMAP'      => $canApplyMAP
                );

                $responseObject = new Varien_Object();
                $args = array('response_object' => $responseObject, 'selection' => $_selection);
                Mage::dispatchEvent('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) {
                    foreach ($responseObject->getAdditionalOptions() as $o=>$v) {
                        $selection[$o] = $v;
                    }
                }
                $option['selections'][$selectionId] = $selection;

                if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired()))
                    && $_selection->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
        }

        $config = array(
            'options'       => $options,
            'selected'      => $selected,
            'bundleId'      => $currentProduct->getId(),
            'priceFormat'   => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice'     => $coreHelper->currency($currentProduct->getPrice(), false, false),
            'priceType'     => $currentProduct->getPriceType(),
            'specialPrice'  => $currentProduct->getSpecialPrice(),
            'includeTax'    => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice'  => $this->getProduct()->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED,
            'isMAPAppliedDirectly' => Mage::helper('catalog')->canApplyMsrp($this->getProduct(), null, false)
        );

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $coreHelper->jsonEncode($config);
    }

    public function addRenderer($type, $block)
    {
        $this->_optionRenderers[$type] = $block;
    }

    public function getOptionHtml($option)
    {
        if (!isset($this->_optionRenderers[$option->getType()])) {
            return $this->__('There is no defined renderer for "%s" option type.', $option->getType());
        }
        return $this->getLayout()->createBlock($this->_optionRenderers[$option->getType()])
            ->setOption($option)->toHtml();
    }
}
