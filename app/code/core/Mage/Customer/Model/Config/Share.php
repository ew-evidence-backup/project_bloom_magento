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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Customer sharing config model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Config_Share extends Mage_Core_Model_Config_Data
{
    /**
     * Xml config path to customers sharing scope value
     *
     */
    const XML_PATH_CUSTOMER_ACCOUNT_SHARE = 'customer/account_share/scope';
    
    /**
     * Possible customer sharing scopes
     *
     */
    const SHARE_GLOBAL  = 0;
    const SHARE_WEBSITE = 1;

    /**
     * Check whether current customers sharing scope is global
     *
     * @return bool
     */
    public function isGlobalScope()
    {
        return !$this->isWebsiteScope();
    }

    /**
     * Check whether current customers sharing scope is website
     *
     * @return bool
     */
    public function isWebsiteScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ACCOUNT_SHARE) == self::SHARE_WEBSITE;
    }

    /**
     * Get possible sharing configuration options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::SHARE_GLOBAL  => Mage::helper('customer')->__('Global'),
            self::SHARE_WEBSITE => Mage::helper('customer')->__('Per Website'),
        );
    }

    /**
     * Check for email dublicates before saving customers sharing options
     *
     * @return Mage_Customer_Model_Config_Share
     * @throws Mage_Core_Exception
     */
    public function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            if (Mage::getResourceSingleton('customer/customer')->findEmailDuplicates()) {
                Mage::throwException(
                    Mage::helper('customer')->__('Cannot share customer accounts globally because some customer accounts with the same emails exist on multiple websites and cannot be merged.')
                );
            }
        }
        return $this;
    }
}
