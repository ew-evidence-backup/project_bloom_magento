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
 * @package     Mage_GoogleBase
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * GoogleBase Item Types collection
 *
 * @deprecated after 1.5.1.0
 * @category    Mage
 * @package     Mage_GoogleBase
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Resource_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('googlebase/type');
    }

    /**
     * Init collection select
     *
     * @return Mage_GoogleBase_Model_Resource_Type_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinAttributeSet();
        return $this;
    }

    /**
     * Add total count of Items for each type
     *
     * @return Mage_GoogleBase_Model_Resource_Type_Collection
     */
    public function addItemsCount()
    {
        $innerSelect = $this->getConnection()->select()
            ->from(
                array('inner_items' => $this->getTable('googlebase/items')),
                array('type_id', 'cnt' => new Zend_Db_Expr('COUNT(inner_items.item_id)'))
            )
            ->group('inner_items.type_id');

        $this->getSelect()
            ->joinLeft(
                array('items' => $innerSelect),
                'main_table.type_id=items.type_id',
                array('items_total' => 'items.cnt'));

        return $this;
    }

    /**
     * Add country ISO filter to collection
     *
     * @param string $iso Two-letter country ISO code
     * @return Mage_GoogleBase_Model_Resource_Type_Collection
     */
    public function addCountryFilter($iso)
    {
        $this->getSelect()->where('target_country=?', $iso);
        return $this;
    }

    /**
     * Join Attribute Set data
     *
     * @return Mage_GoogleBase_Model_Resource_Type_Collection
     */
    protected function _joinAttributeSet()
    {
        $this->getSelect()
            ->join(
                array('a_set'=>$this->getTable('eav/attribute_set')),
                'main_table.attribute_set_id=a_set.attribute_set_id',
                array('attribute_set_name' => 'a_set.attribute_set_name'));
        return $this;
    }
}
