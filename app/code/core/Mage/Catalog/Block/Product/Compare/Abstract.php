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
 * Catalog Compare Products Abstract Block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Product_Compare_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Retrieve Product Compare Helper
     *
     * @return Mage_Catalog_Helper_Product_Compare
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog/product_compare');
    }

    /**
     * Retrieve Remove Item from Compare List URL
     *
     * @param Mage_Catalog_Model_Product $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }
}
