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
 * Dashboard Customer Info
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Customer_Block_Account_Dashboard_Info extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getChangePasswordUrl()
    {
        return Mage::getUrl('*/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getSubscriptionObject()
    {
        if(is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
        }

        return $this->_subscription;
    }

    /**
     * Gets Customer subscription status
     *
     * @return bool
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     *  Newsletter module availability
     *
     *  @return	  boolean
     */
    public function isNewsletterEnabled()
    {
        return $this->getLayout()->getBlockSingleton('customer/form_register')->isNewsletterEnabled();
    }
}
