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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Google Checkout shipping model
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 */
class Mage_GoogleCheckout_Model_Shipping extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'googlecheckout';

    /**
     * Collects rates for user request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        // dummy placeholder
        return $this;
    }

    /**
     * Returns array(methodCode => methodName) of possible methods for this carrier
     * Used to automatically show it in config and so on
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array();
    }

    /**
     * Returns array(methodCode => methodName) of internally used methods.
     * They are possible only as result of completing Google Checkout.
     *
     * @return array
     */
    public function getInternallyAllowedMethods()
    {
        return array(
            'carrier'  => 'Carrier',
            'merchant' => 'Merchant',
            'flatrate' => 'Flat Rate',
            'pickup'   => 'Pickup'
        );
    }
}
