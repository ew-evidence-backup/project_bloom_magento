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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Google Checkout resource notification model
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleCheckout_Model_Resource_Notification extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Intialize resource model.
     * Set main entity table name and primary key field name.
     *
     */
    protected function _construct()
    {
        $this->_init('googlecheckout/notification', 'serial_number');
    }

    /**
     * Return notification data by serial number
     *
     * @param string $serialNumber
     * @return array
     */
    public function getNotificationData($serialNumber)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('*'))
            ->where('serial_number = ?', $serialNumber);

        return $this->_getReadAdapter()->fetchRow($select);
    }

    /**
     * Start notification processing
     *
     * @param string $serialNumber
     * @return Mage_GoogleCheckout_Model_Resource_Notification
     */
    public function startProcess($serialNumber)
    {
        $data = array(
            'serial_number' => $serialNumber,
            'started_at'    => Varien_Date::now(),
            'status'        => Mage_GoogleCheckout_Model_Notification::STATUS_INPROCESS
        );
        $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        return $this;
    }

    /**
     * Stop notification processing
     *
     * @param string $serialNumber
     * @return Mage_GoogleCheckout_Model_Resource_Notification
     */
    public function stopProcess($serialNumber)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('status' => Mage_GoogleCheckout_Model_Notification::STATUS_PROCESSED),
            array('serial_number = ?' => $serialNumber)
        );
        return $this;
    }

    /**
     * Update notification processing
     *
     * @param string $serialNumber
     * @return Mage_GoogleCheckout_Model_Resource_Notification
     */
    public function updateProcess($serialNumber)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('started_at' => Varien_Date::now()),
            array('serial_number = ?' => $serialNumber)
        );

        return $this;
    }
}
