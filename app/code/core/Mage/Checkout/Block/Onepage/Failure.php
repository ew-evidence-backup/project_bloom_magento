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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


class Mage_Checkout_Block_Onepage_Failure extends Mage_Core_Block_Template
{
    public function getRealOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastRealOrderId();
    }

    /**
     *  Payment custom error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $error = Mage::getSingleton('checkout/session')->getErrorMessage();
        // Mage::getSingleton('checkout/session')->unsErrorMessage();
        return $error;
    }

    /**
     * Continue shopping URL
     *
     *  @return	  string
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
