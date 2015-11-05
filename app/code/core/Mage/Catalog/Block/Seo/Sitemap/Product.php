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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * SEO Products Sitemap block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Seo_Sitemap_Product extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize products collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */

        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('url_key');
        $collection->addStoreFilter();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->setCollection($collection);

        return $this;
    }

    /**
     * Get item URL
     *
     * @param Mage_Catalog_Model_Product $category
     * @return string
     */
    public function getItemUrl($product)
    {
        $helper = Mage::helper('catalog/product');
        /* @var $helper Mage_Catalog_Helper_Product */
        return $helper->getProductUrl($product);
    }

}
