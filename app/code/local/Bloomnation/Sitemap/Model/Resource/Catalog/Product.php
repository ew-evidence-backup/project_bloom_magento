<?php

class Bloomnation_Sitemap_Model_Resource_Catalog_Product extends Mage_Sitemap_Model_Resource_Catalog_Product
{
  protected function _prepareProduct(array $productRow)
  {
    $product = new Varien_Object();
    $product->setId($productRow[$this->getIdFieldName()]);
    $id = $product->getId();

    $productMedia = Mage::getModel('catalog/product')->load($id)->getImage();
    $product->setMedia($productMedia);
    $productName = Mage::getModel('catalog/product')->load($id)->getName();
    $product->setName($productName);
    $productUrl = !empty($productRow['url']) ? $productRow['url']: 'catalog/product/view/id/' . $product->getId();
    $product->setUrl($productUrl);
    return $product;
  }
}