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
 * @package     Enterprise_CustomerBalance
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Js extends Mage_Adminhtml_Block_Template
{
    public function getCustomerWebsite()
    {
        return Mage::registry('current_customer')->getWebsiteId();
    }

    public function getWebsitesJson()
    {
        $result = array();
        foreach (Mage::app()->getWebsites() as $websiteId => $website) {
            $result[$websiteId] = array(
                'name'          => $website->getName(),
                'website_id'    => $websiteId,
                'currency_code' => $website->getBaseCurrencyCode(),
                'groups'        => array()
            );

            foreach ($website->getGroups() as $groupId => $group) {
                $result[$websiteId]['groups'][$groupId] = array(
                    'name' => $group->getName()
                );

                foreach ($group->getStores() as $storeId => $store) {
                    $result[$websiteId]['groups'][$groupId]['stores'][] = array(
                        'name'     => $store->getName(),
                        'store_id' => $storeId,
                    );
                }
            }
        }

        return Mage::helper('core')->jsonEncode($result);
    }
}
