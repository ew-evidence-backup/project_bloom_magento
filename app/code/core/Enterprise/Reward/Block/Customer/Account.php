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
 * Customer Account empty block (using only just for adding RP link to tab)
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_Account extends Mage_Core_Block_Abstract
{
    /**
     * Add RP link to tab if we have all rates
     *
     * @return Enterprise_Reward_Block_Customer_Account
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock && Mage::helper('enterprise_reward')->isEnabledOnFront()) {
            $navigationBlock->addLink('enterprise_reward', 'enterprise_reward/customer/info/',
                Mage::helper('enterprise_reward')->__('Reward Points'));
        }
        return $this;
    }
}
