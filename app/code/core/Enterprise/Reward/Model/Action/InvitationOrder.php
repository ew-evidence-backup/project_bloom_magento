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
 * Reward action to add points to inviter when his referral purchases order
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Action_InvitationOrder extends Enterprise_Reward_Model_Action_Abstract
{
    /**
     * Retrieve points delta for action
     *
     * @param int $websiteId
     * @return int
     */
    public function getPoints($websiteId)
    {
        return (int)Mage::helper('enterprise_reward')->getPointsConfig('invitation_order', $websiteId);
    }

    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        $frequency = Mage::helper('enterprise_reward')->getPointsConfig(
            'invitation_order_frequency', $this->getReward()->getWebsiteId()
        );
        if ($frequency == '*') {
            return !($this->isRewardLimitExceeded());
        } else {
            return parent::canAddRewardPoints();
        }
    }

    /**
     * Return pre-configured limit of rewards for action
     *
     * @return int|string
     */
    public function getRewardLimit()
    {
        return Mage::helper('enterprise_reward')->getPointsConfig('invitation_order_limit', $this->getReward()->getWebsiteId());
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return string
     */
    public function getHistoryMessage($args = array())
    {
        $email = isset($args['email']) ? $args['email'] : '';
        return Mage::helper('enterprise_reward')->__('Invitation to %s converted into an order.', $email);
    }

    /**
     * Setter for $_entity and add some extra data to history
     *
     * @param Enterprise_Invitation_Model_Invitation $entity
     * @return Enterprise_Reward_Model_Action_Abstract
     */
    public function setEntity($entity)
    {
        parent::setEntity($entity);
        $this->getHistory()->addAdditionalData(array('email' => $this->getEntity()->getEmail()));
        return $this;
    }
}
