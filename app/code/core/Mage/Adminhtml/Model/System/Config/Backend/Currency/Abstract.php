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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Directory currency abstract backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Adminhtml_Model_System_Config_Backend_Currency_Abstract extends Mage_Core_Model_Config_Data
{
    /**
     * Retrieve allowed currencies for current scope
     *
     * @return array
     */
    protected function _getAllowedCurrencies()
    {
        if ($this->getData('groups/options/fields/allow/inherit')) {
            return explode(',', Mage::getConfig()->getNode('currency/options/allow', $this->getScope(), $this->getScopeId()));
        }
        return $this->getData('groups/options/fields/allow/value');
    }

    /**
     * Retrieve Installed Currencies
     *
     * @return array
     */
    protected function _getInstalledCurrencies()
    {
        return explode(',', Mage::getStoreConfig('system/currency/installed'));
    }

    /**
     * Retrieve Base Currency value for current scope
     *
     * @return string
     */
    protected function _getCurrencyBase()
    {
        if (!$value = $this->getData('groups/options/fields/base/value')) {
            $value = Mage::getConfig()->getNode(
                Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE,
                $this->getScope(),
                $this->getScopeId()
            );
        }
        return strval($value);
    }

    /**
     * Retrieve Default desplay Currency value for current scope
     *
     * @return string
     */
    protected function _getCurrencyDefault()
    {
        if (!$value = $this->getData('groups/options/fields/default/value')) {
            $value = Mage::getConfig()->getNode(
                Mage_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT,
                $this->getScope(),
                $this->getScopeId()
            );
        }
        return strval($value);
    }
}
