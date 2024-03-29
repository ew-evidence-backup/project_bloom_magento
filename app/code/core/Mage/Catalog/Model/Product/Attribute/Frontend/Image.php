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
 * Product image attribute frontend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Frontend_Image extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{

    public function getUrl($object, $size=null)
    {
        $url = false;
        $image = $object->getData($this->getAttribute()->getAttributeCode());

        if( !is_null($size) && file_exists(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS.$size.DS.$image) ) {
            # resized image is cached
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media').'catalog/product/' . $size . '/' . $image;
        } elseif( !is_null($size) ) {
            # resized image is not cached
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl().'catalog/product/image/size/' . $size . '/' . $image;
        } elseif ($image) {
            # using original image
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media').'catalog/product/'.$image;
        }
        return $url;
    }

}
