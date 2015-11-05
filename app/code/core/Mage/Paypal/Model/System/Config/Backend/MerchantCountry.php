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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Backend model for merchant country. Default country used instead of empty value.
 */
class Mage_Paypal_Model_System_Config_Backend_MerchantCountry extends Mage_Core_Model_Config_Data
{
    /**
     * Config path to default country
     * @deprecated since 1.4.1.0
     * @var string
     */
    const XML_PATH_COUNTRY_DEFAULT = 'general/country/default';

    /**
     * Substitute empty value with Default country.
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (empty($value)) {
            if ($this->getWebsite()) {
                $defaultCountry = Mage::app()->getWebsite($this->getWebsite())
                    ->getConfig(Mage_Core_Helper_Data::XML_PATH_DEFAULT_COUNTRY);
            } else {
                $defaultCountry = Mage::helper('core')->getDefaultCountry($this->getStore());
            }
            $this->setValue($defaultCountry);
        }
    }
}
