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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Flat sales order address resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Address extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_resource';

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/order_address', 'entity_id');
    }

    /**
     * Return configuration for all attributes
     *
     * @return array
     */
    public function getAllAttributes()
    {
        $attributes = array(
            'city'       => Mage::helper('sales')->__('City'),
            'company'    => Mage::helper('sales')->__('Company'),
            'country_id' => Mage::helper('sales')->__('Country'),
            'email'      => Mage::helper('sales')->__('Email'),
            'firstname'  => Mage::helper('sales')->__('First Name'),
            'lastname'   => Mage::helper('sales')->__('Last Name'),
            'region_id'  => Mage::helper('sales')->__('State/Province'),
            'street'     => Mage::helper('sales')->__('Street Address'),
            'telephone'  => Mage::helper('sales')->__('Telephone'),
            'postcode'   => Mage::helper('sales')->__('Zip/Postal Code')
        );
        asort($attributes);
        return $attributes;
    }
}
