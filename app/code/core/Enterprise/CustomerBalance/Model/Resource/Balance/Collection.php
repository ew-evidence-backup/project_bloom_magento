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


/**
 * Customerbalance history collection
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerBalance_Model_Resource_Balance_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_customerbalance/balance');
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Enterprise_CustomerBalance_Model_Resource_Balance_Collection
     */
    public function addWebsitesFilter($websiteIds)
    {
        $this->getSelect()->where(
            array('main_table.website_id IN (?)' => $websiteIds)
        );
        return $this;
    }

    /**
     * Implement after load logic for each collection item
     *
     * @return Enterprise_CustomerBalance_Model_Resource_Balance_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->walk('afterLoad');
        return $this;
    }
}
