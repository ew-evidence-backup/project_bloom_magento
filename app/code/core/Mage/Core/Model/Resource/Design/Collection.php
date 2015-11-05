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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Core Design resource collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Design_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Core Design resource collection
     *
     */
    protected function _construct()
    {
        $this->_init('core/design');
    }

    /**
     * Join store data to collection
     *
     * @return Mage_Core_Model_Resource_Design_Collection
     */
    public function joinStore()
    {
         return $this->join(
            array('cs' => $this->getTable('core/store')),
            'cs.store_id = main_table.store_id',
            array('cs.name'));
    }

    /**
     * Add date filter to collection
     *
     * @param null|int|string|Zend_Date $date
     * @return Mage_Core_Model_Resource_Design_Collection
     */
    public function addDateFilter($date = null)
    {
        if (is_null($date)) {
            $date = $this->formatDate(true);
        } else {
            $date = $this->formatDate($date);
        }

        $this->addFieldToFilter('date_from', array('lteq' => $date));
        $this->addFieldToFilter('date_to', array('gteq' => $date));
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @param int|array $storeId
     * @return Mage_Core_Model_Resource_Design_Collection
     */
    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', array('in' => $storeId));
    }
}
