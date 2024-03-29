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
 * Source model for available settlement report fetching intervals
 */
class Mage_Paypal_Model_System_Config_Source_FetchingSchedule
{
    public function toOptionArray()
    {
        return array (
            1 => Mage::helper('paypal')->__("Daily"),
            3 => Mage::helper('paypal')->__("Every 3 days"),
            7 => Mage::helper('paypal')->__("Every 7 days"),
            10 => Mage::helper('paypal')->__("Every 10 days"),
            14 => Mage::helper('paypal')->__("Every 14 days"),
            30 => Mage::helper('paypal')->__("Every 30 days"),
            40 => Mage::helper('paypal')->__("Every 40 days"),
        );
    }
}
