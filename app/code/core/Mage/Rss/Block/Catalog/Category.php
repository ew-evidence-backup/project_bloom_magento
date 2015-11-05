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
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_Category extends Mage_Rss_Block_Catalog_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_category_'
            . $this->getRequest()->getParam('cid') . '_'
            . $this->getRequest()->getParam('store_id') . '_'
            . Mage::getModel('customer/session')->getId()
        );
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $categoryId = $this->getRequest()->getParam('cid');
        $storeId = $this->_getStoreId();
        $rssObj = Mage::getModel('rss/rss');
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category && $category->getId()) {
                $layer = Mage::getSingleton('catalog/layer')->setStore($storeId);
                //want to load all products no matter anchor or not
                $category->setIsAnchor(true);
                $newurl = $category->getUrl();
                $title = $category->getName();
                $data = array('title' => $title,
                        'description' => $title,
                        'link'        => $newurl,
                        'charset'     => 'UTF-8',
                        );

                $rssObj->_addHeader($data);

                $_collection = $category->getCollection();
                $_collection->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active',1)
                    ->addIdFilter($category->getChildren())
                    ->load()
                ;
                $productCollection = Mage::getModel('catalog/product')->getCollection();

                $currentCategory = $layer->setCurrentCategory($category);
                $layer->prepareProductCollection($productCollection);
                $productCollection->addCountToCategories($_collection);

                $category->getProductCollection()->setStoreId($storeId);
                /*
                only load latest 50 products
                */
                $_productCollection = $currentCategory
                    ->getProductCollection()
                    ->addAttributeToSort('updated_at','desc')
                    ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
                    ->setCurPage(1)
                    ->setPageSize(50)
                ;

                if ($_productCollection->getSize()>0) {
                    $args = array('rssObj' => $rssObj);
                    foreach ($_productCollection as $_product) {
                        $args['product'] = $_product;
                        $this->addNewItemXmlCallback($args);
                    }
                }
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);

        Mage::dispatchEvent('rss_catalog_category_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            return;
        }

        $description = '<table><tr>'
                     . '<td><a href="'.$product->getProductUrl().'"><img src="'
                     . $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($product->getAllowedPriceInRss()) {
            $description.= $this->getPriceHtml($product,true);
        }

        $description .= '</td></tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,
            );

        $rssObj->_addEntry($data);
    }
}