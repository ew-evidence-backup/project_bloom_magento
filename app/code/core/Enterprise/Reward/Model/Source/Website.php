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
 * Source model for websites, including "All" option
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Source_Website
{
    /**
     * Prepare and return array of website ids and their names
     *
     * @param bool $withAll Whether to prepend "All websites" option on not
     * @return array
     */
    public function toOptionArray($withAll = true)
    {
        $websites = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash();
        if ($withAll) {
            $websites = array(0 => Mage::helper('enterprise_reward')->__('All Websites'))
                      + $websites;
        }
        return $websites;
    }
}
