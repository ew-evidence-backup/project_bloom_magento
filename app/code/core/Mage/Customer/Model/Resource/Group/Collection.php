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
 * Customer group collection
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Group_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('customer/group');
    }

    /**
     * Set tax group filter
     *
     * @param mixed $classId
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function setTaxGroupFilter($classId)
    {
        $this->getSelect()->joinLeft(
            array('tax_class_group' => $this->getTable('tax/tax_class_group')),
            'tax_class_group.class_group_id = main_table.customer_group_id'
        );
        $this->addFieldToFilter('tax_class_group.class_parent_id', $classId);
        return $this;
    }

    /**
     * Set ignore ID filter
     *
     * @param array $indexes
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function setIgnoreIdFilter($indexes)
    {
        if (count($indexes)) {
            $this->addFieldToFilter('main_table.customer_group_id', array('nin' => $indexes));
        }
        return $this;
    }

    /**
     * Set real groups filter
     *
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function setRealGroupsFilter()
    {
        return $this->addFieldToFilter('customer_group_id', array('gt' => 0));
    }

    /**
     * Add tax class
     *
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function addTaxClass()
    {
        $this->getSelect()->joinLeft(
            array('tax_class_table' => $this->getTable('tax/tax_class')),
            "main_table.tax_class_id = tax_class_table.class_id");
        return $this;
    }

    /**
     * Retreive option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }

    /**
     * Retreive option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('customer_group_id', 'customer_group_code');
    }
}
