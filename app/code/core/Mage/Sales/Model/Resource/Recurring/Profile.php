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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Recurring payment profiles resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Recurring_Profile extends Mage_Sales_Model_Resource_Abstract
{
    /**
     * Initialize main table and column
     *
     */
    protected function _construct()
    {
        $this->_init('sales/recurring_profile', 'profile_id');

        $this->_serializableFields = array(
            'profile_vendor_info'    => array(null, array()),
            'additional_info' => array(null, array()),

            'order_info' => array(null, array()),
            'order_item_info' => array(null, array()),
            'billing_address_info' => array(null, array()),
            'shipping_address_info' => array(null, array())
        );
    }

    /**
     * Return recurring profile child Orders Ids
     *
     *
     * @param Varien_Object $object
     * @return array
     */
    public function getChildOrderIds($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(':profile_id' => $object->getId());
        $select  = $adapter->select()
            ->from(
                array('main_table' => $this->getTable('sales/recurring_profile_order')),
                array('order_id'))
            ->where('profile_id=:profile_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Add order relation to recurring profile
     *
     * @param int $recurringProfileId
     * @param int $orderId
     * @return Mage_Sales_Model_Resource_Recurring_Profile
     */
    public function addOrderRelation($recurringProfileId, $orderId)
    {
        $this->_getWriteAdapter()->insert(
            $this->getTable('sales/recurring_profile_order'), array(
                'profile_id' => $recurringProfileId,
                'order_id'   => $orderId
            )
        );
        return $this;
    }
}
