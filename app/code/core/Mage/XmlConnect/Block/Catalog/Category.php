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
 * Category list xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Category extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $categoryXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $categoryXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<category></category>');
        $categoryId     = $this->getRequest()->getParam('id', null);
        if ($categoryId === null) {
            $categoryId = Mage::app()->getStore()->getRootCategoryId();
        }

        $productsXmlObj = $productListBlock = false;
        /** @var $categoryModel Mage_Catalog_Model_Category */
        $categoryModel  = Mage::getModel('catalog/category')->load($categoryId);
        if ($categoryModel->getId()) {
            $hasMoreProductItems = 0;
            $productListBlock = $this->getChild('product_list');
            if ($productListBlock && $categoryModel->getLevel() > 1) {
                $layer = Mage::getSingleton('catalog/layer');
                $productsXmlObj = $productListBlock->setCategory($categoryModel)->setLayer($layer)
                    ->getProductsXmlObject();
                $hasMoreProductItems = (int)$productListBlock->getHasProductItems();
            }

            $infoBlock = $this->getChild('category_info');
            if ($infoBlock) {
                $categoryInfoXmlObj = $infoBlock->setCategory($categoryModel)->getCategoryInfoXmlObject();
                $categoryInfoXmlObj->addChild('has_more_items', $hasMoreProductItems);
                $categoryXmlObj->appendChild($categoryInfoXmlObj);
            }
        }

        $categoryCollection = $this->getCurrentChildCategories();

        // subcategories are exists
        if (sizeof($categoryCollection)) {
            $itemsXmlObj = $categoryXmlObj->addChild('items');
            foreach ($categoryCollection as $item) {
                /** @var $item Mage_Catalog_Model_Category */
                $item = Mage::getModel('catalog/category')->load($item->getId());

                $itemXmlObj = $itemsXmlObj->addChild('item');
                $itemXmlObj->addChild('label', $categoryXmlObj->xmlentities($item->getName()));
                $itemXmlObj->addChild('entity_id', $item->getId());
                $itemXmlObj->addChild('content_type', $item->hasChildren() ? 'categories' : 'products');
                if (!is_null($categoryId)) {
                    $itemXmlObj->addChild('parent_id', $item->getParentId());
                }
                $icon = Mage::helper('xmlconnect/catalog_category_image')->initialize($item, 'thumbnail')
                    ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('category'));

                $iconXml = $itemXmlObj->addChild('icon', $icon);

                $file = Mage::helper('xmlconnect')->urlToPath($icon);
                $iconXml->addAttribute('modification_time', filemtime($file));
            }
        }

        if ($productListBlock && $productsXmlObj) {
            $categoryXmlObj->appendChild($productsXmlObj);
        }
        return $categoryXmlObj->asNiceXml();
    }

    /**
     * Retrieve child categories of current category
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getCurrentChildCategories()
    {
        $layer = Mage::getSingleton('catalog/layer');
        $category   = $layer->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $categories = $category->getChildrenCategories();
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($categories);
        return $categories;
    }
}
