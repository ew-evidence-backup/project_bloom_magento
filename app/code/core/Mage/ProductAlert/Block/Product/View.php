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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Product view price and stock alerts
 */
class Mage_ProductAlert_Block_Product_View extends Mage_Core_Block_Template
{
    /**
     * Current product instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    /**
     * Check whether the stock alert data can be shown and prepare related data
     */
    public function prepareStockAlertData()
    {
        if (!Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_STOCK_ALLOW)
            || !$this->_product || $this->_product->isAvailable()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl(Mage::helper('productalert')->getSaveUrl('stock'));
    }

    /**
     * Check whether the price alert data can be shown and prepare related data
     */
    public function preparePriceAlertData()
    {
        if (!Mage::getStoreConfigFlag(Mage_ProductAlert_Model_Observer::XML_PATH_PRICE_ALLOW)
            || !$this->_product || false === $this->_product->getCanShowPrice()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl(Mage::helper('productalert')->getSaveUrl('price'));
    }

    /**
     * Get current product instance
     *
     * @return Mage_ProductAlert_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $product = Mage::registry('current_product');
        if ($product && $product->getId()) {
            $this->_product = $product;
        }

        return parent::_prepareLayout();
    }
}
