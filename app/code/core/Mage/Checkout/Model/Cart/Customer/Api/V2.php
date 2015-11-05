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

/**
 * Shoping cart api for customer data 
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Cart_Customer_Api_V2 extends Mage_Checkout_Model_Cart_Customer_Api
{
    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array
     */
    protected function _prepareCustomerData($data)
    {
        if (null !== ($_data = get_object_vars($data))) {
            return parent::_prepareCustomerData($_data);
        }
        return array();
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array
     */
    protected function _prepareCustomerAddressData($data)
    {
        if (is_array($data)) {
            $dataAddresses = array();
            foreach($data as $addressItem) {
                if (null !== ($_addressItem = get_object_vars($addressItem))) {
                    $dataAddresses[] = $_addressItem;
                }
            }
            return parent::_prepareCustomerAddressData($dataAddresses);
        }
        
        return null;
    }
}
