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
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Reward Helper for operations with customer
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Helper_Customer extends Mage_Core_Helper_Abstract
{
    /**
     * Return Unsubscribe notification URL
     *
     * @param string $notification Notification type
     * @return string
     */
    public function getUnsubscribeUrl($notification = false, $storeId = null)
    {
        $params = array();
        if ($notification) {
            $params['notification'] = $notification;
        }
        if (!is_null($storeId)) {
            $params['store_id'] = $storeId;
        }
        return Mage::getUrl('enterprise_reward/customer/unsubscribe/', $params);
    }
}
