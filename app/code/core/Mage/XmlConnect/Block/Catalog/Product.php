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
 * Product data xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Retrieve product attributes as xml object
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $itemNodeName
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function productToXmlObject(Mage_Catalog_Model_Product $product, $itemNodeName = 'item')
    {
        /** @var $item Mage_XmlConnect_Model_Simplexml_Element */
        $item = Mage::getModel('xmlconnect/simplexml_element', '<' . $itemNodeName . '></' . $itemNodeName . '>');
        if ($product && $product->getId()) {
            $item->addChild('entity_id', $product->getId());
            $item->addChild('name', $item->xmlentities($product->getName()));
            $item->addChild('entity_type', $product->getTypeId());
            $item->addChild('short_description', $item->xmlentities($product->getShortDescription()));
            $description = Mage::helper('xmlconnect')->htmlize($item->xmlentities($product->getDescription()));
            $item->addChild('description', $description);
            $item->addChild('link', $product->getProductUrl());

            if ($itemNodeName == 'item') {
                $imageToResize = Mage::helper('xmlconnect/image')->getImageSizeForContent('product_small');
                $propertyToResizeName = 'small_image';
            } else {
                $imageToResize = Mage::helper('xmlconnect/image')->getImageSizeForContent('product_big');
                $propertyToResizeName = 'image';
            }

            $icon = clone Mage::helper('catalog/image')->init($product, $propertyToResizeName)->resize($imageToResize);

            $iconXml = $item->addChild('icon', $icon);

            $file = Mage::helper('xmlconnect')->urlToPath($icon);
            $iconXml->addAttribute('modification_time', filemtime($file));

            $item->addChild('in_stock', (int)$product->getIsInStock());
            $item->addChild('is_salable', (int)$product->isSalable());
            /**
             * By default all products has gallery (because of collection not load gallery attribute)
             */
            $hasGallery = 1;
            if ($product->getMediaGalleryImages()) {
                $hasGallery = sizeof($product->getMediaGalleryImages()) > 0 ? 1 : 0;
            }
            $item->addChild('has_gallery', $hasGallery);
            /**
             * If product type is grouped than it has options as its grouped items
             */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                $product->setHasOptions(true);
            }
            $item->addChild('has_options', (int)$product->getHasOptions());

            if ($minSaleQty = $this->_getMinimalQty($product)) {
                $item->addChild('min_sale_qty', (int) $minSaleQty);
            }

            if (!$product->getRatingSummary()) {
                Mage::getModel('review/review')->getEntitySummary($product, Mage::app()->getStore()->getId());
            }

            $item->addChild('rating_summary', round((int)$product->getRatingSummary()->getRatingSummary() / 10));
            $item->addChild('reviews_count', $product->getRatingSummary()->getReviewsCount());

            if ($this->getChild('product_price')) {
                $this->getChild('product_price')->setProduct($product)->setProductXmlObj($item)
                    ->collectProductPrices();
            }

            if ($this->getChild('additional_info')) {
                $this->getChild('additional_info')->addAdditionalData($product, $item);
            }
        }

        return $item;
    }

    /**
     * Get MinSaleQty for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int|null
     */
    protected function _getMinimalQty($product)
    {
        if ($stockItem = $product->getStockItem()) {
            if ($stockItem->getMinSaleQty() && $stockItem->getMinSaleQty() > 0) {
                return ($stockItem->getMinSaleQty() * 1);
            }
        }
        return null;
    }

    /**
     * Render product info xml
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->getRequest()->getParam('id', 0));

        if (!$product) {
            Mage::throwException($this->__('Selected product is unavailable.'));
        } else {
            $this->setProduct($product);
            $productXmlObj = $this->productToXmlObject($product, 'product');

            $relatedProductsBlock = $this->getChild('related_products');
            if ($relatedProductsBlock) {
                $relatedXmlObj = $relatedProductsBlock->getRelatedProductsXmlObj();
                $productXmlObj->appendChild($relatedXmlObj);
            }
        }

        $productOptions = $this->getChild('xmlconnect.catalog.product.options')
            ->getProductOptionsXmlObject($product);
        if ($productOptions instanceof Mage_XmlConnect_Model_Simplexml_Element) {
            $productXmlObj->appendChild($productOptions);
        }

        return $productXmlObj->asNiceXml();
    }
}
