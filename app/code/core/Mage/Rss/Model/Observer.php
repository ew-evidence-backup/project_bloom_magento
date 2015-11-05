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
 * Rss Observer Model
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Model_Observer
{

    /**
     * Clean cache for catalog review rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(Mage_Rss_Block_Catalog_Review::CACHE_TAG));

    }

    /**
     * Clean cache for notify stock rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderItemSaveAfterNotifyStock(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(Mage_Rss_Block_Catalog_NotifyStock::CACHE_TAG));

    }

    /**
     * Clean cache for catalog new orders rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderItemSaveAfterOrderNew(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(Mage_Rss_Block_Order_New::CACHE_TAG));

    }
}
