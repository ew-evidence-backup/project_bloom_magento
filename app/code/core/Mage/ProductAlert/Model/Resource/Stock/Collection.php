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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Product alert for back in stock collection
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Resource_Stock_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define stock collection
     *
     */
    protected function _construct()
    {
        $this->_init('productalert/stock');
    }

    /**
     * Add customer filter
     *
     * @param mixed $customer
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function addCustomerFilter($customer)
    {
        $adapter = $this->getConnection();
        if (is_array($customer)) {
            $condition = $adapter->quoteInto('customer_id IN(?)', $customer);
        } elseif ($customer instanceof Mage_Customer_Model_Customer) {
            $condition = $adapter->quoteInto('customer_id=?', $customer->getId());
        } else {
            $condition = $adapter->quoteInto('customer_id=?', $customer);
        }
        $this->addFilter('customer_id', $condition, 'string');
        return $this;
    }

    /**
     * Add website filter
     *
     * @param mixed $website
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function addWebsiteFilter($website)
    {
        $adapter = $this->getConnection();
        if (is_null($website) || $website == 0) {
            return $this;
        }
        if (is_array($website)) {
            $condition = $adapter->quoteInto('website_id IN(?)', $website);
        } elseif ($website instanceof Mage_Core_Model_Website) {
            $condition = $adapter->quoteInto('website_id=?', $website->getId());
        } else {
            $condition = $adapter->quoteInto('website_id=?', $website);
        }
        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    /**
     * Add status filter
     *
     * @param int $status
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function addStatusFilter($status)
    {
        $condition = $this->getConnection()->quoteInto('status=?', $status);
        $this->addFilter('status', $condition, 'string');
        return $this;
    }

    /**
     * Set order by customer
     *
     * @param string $sort
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function setCustomerOrder($sort = 'ASC')
    {
        $this->getSelect()->order('customer_id ' . $sort);
        return $this;
    }
}
