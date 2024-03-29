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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Reports tax collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Tax_Collection extends Mage_Sales_Model_Entity_Order_Collection
{
    /**
     * Set row identifier field name
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setRowIdFieldName('tax_id');
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Tax_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset();

        $this->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', array('entity_id'))
            ->getSelect()
            ->join(
                array('tax_table' => $this->getTable('sales/order_tax')),
                'e.entity_id = tax_table.order_id')
            ->group('tax_table.code')
            ->order(array('process', 'priority'));
        /*
         * Allow Analytic Functions Usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Tax_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->getSelect()
                ->where('e.store_id IN(?)', (array)$storeIds)
                ->columns(array('tax' => 'SUM(tax_table.base_real_amount)'));
        } else {
            $this->addExpressionAttributeToSelect(
                'tax',
                'SUM(tax_table.base_real_amount*{{base_to_global_rate}})',
                array('base_to_global_rate')
            );
        }

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns("COUNT(DISTINCT e.entity_id)");
        return $countSelect;
    }
}
