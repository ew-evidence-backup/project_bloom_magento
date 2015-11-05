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
 * Quote addresses shiping rates collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Rate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Whether to load fixed items only
     *
     * @var bool
     */
    protected $_allowFixedOnly   = false;

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/quote_address_rate');
    }

    /**
     * Set filter by address id
     *
     * @param int $addressId
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     */
    public function setAddressFilter($addressId)
    {
        if ($addressId) {
            $this->addFieldToFilter('address_id', $addressId);
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Setter for loading fixed items only
     *
     * @param bool $value
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     */
    public function setFixedOnlyFilter($value)
    {
        $this->_allowFixedOnly = (bool)$value;
        return $this;
    }

    /**
     * Don't add item to the collection if only fixed are allowed and its carrier is not fixed
     *
     * @param Mage_Sales_Model_Quote_Address_Rate $rate
     * @return Mage_Sales_Model_Resource_Quote_Address_Rate_Collection
     */
    public function addItem(Varien_Object $rate)
    {
        if ($this->_allowFixedOnly && (!$rate->getCarrierInstance() || !$rate->getCarrierInstance()->isFixed())) {
            return $this;
        }
        return parent::addItem($rate);
    }
}
