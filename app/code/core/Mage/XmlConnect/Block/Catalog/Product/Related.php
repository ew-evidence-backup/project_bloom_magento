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
 * Related products block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Related extends Mage_XmlConnect_Block_Catalog_Product_List
{
    /**
     * Retrieve related products xml object based on current product
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     * @see $this->getProduct()
     */
    public function getRelatedProductsXmlObj()
    {
        /** @var $relatedXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $relatedXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<related_products></related_products>');

        $productObj = $this->getParentBlock()->getProduct();

        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_TargetRule'))) {
            Mage::register('product', $productObj);

            $productBlock = $this->getLayout()->addBlock(
                'enterprise_targetrule/catalog_product_list_related', 'relatedProducts'
            );

            $collection = $productBlock->getItemCollection();
        } else {
            if ($productObj->getId() > 0) {
                $collection = $this->_getProductCollection();
                if (!$collection) {
                    return $relatedXmlObj;
                }
                $collection = $collection->getItems();
            }
        }

        $this->_addProductXmlObj($relatedXmlObj, $collection);

        return $relatedXmlObj;
    }

    /**
     * Add related products info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $relatedXmlObj
     * @param array $collection
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addProductXmlObj(Mage_XmlConnect_Model_Simplexml_Element $relatedXmlObj, $collection)
    {
        foreach ($collection as $product) {
            $productXmlObj = $this->productToXmlObject($product);

            if (!$productXmlObj) {
                continue;
            }

            if ($this->getParentBlock()->getChild('product_price')) {
                $this->getParentBlock()->getChild('product_price')->setProduct($product)
                    ->setProductXmlObj($productXmlObj)->collectProductPrices();
            }
            $relatedXmlObj->appendChild($productXmlObj);
        }
        return $relatedXmlObj;
    }

    /**
     * Generate related products xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getRelatedProductsXmlObj()->asNiceXml();
    }

    /**
     * Retrieve product collection with all prepared data
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = $this->getParentBlock()->getProduct()->getRelatedProductCollection();
            Mage::getSingleton('catalog/layer')->prepareProductCollection($collection);
            /**
             * Add rating and review summary, image attribute, apply sort params
             */
            $this->_prepareCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
