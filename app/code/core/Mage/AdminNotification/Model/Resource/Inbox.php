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
 * @package     Mage_AdminNotification
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * AdminNotification Inbox model
 *
 * @category    Mage
 * @package     Mage_AdminNotification
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Resource_Inbox extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * AdminNotification Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('adminnotification/inbox', 'notification_id');
    }

    /**
     * Load latest notice
     *
     * @param Mage_AdminNotification_Model_Inbox $object
     * @return Mage_AdminNotification_Model_Resource_Inbox
     */
    public function loadLatestNotice(Mage_AdminNotification_Model_Inbox $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->order($this->getIdFieldName() . ' DESC')
            ->where('is_read != 1')
            ->where('is_remove != 1')
            ->limit(1);
        $data = $adapter->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Get notifications grouped by severity
     *
     * @param Mage_AdminNotification_Model_Inbox $object
     * @return array
     */
    public function getNoticeStatus(Mage_AdminNotification_Model_Inbox $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array(
                'severity'     => 'severity',
                'count_notice' => new Zend_Db_Expr('COUNT(' . $this->getIdFieldName() . ')')))
            ->group('severity')
            ->where('is_remove=?', 0)
            ->where('is_read=?', 0);
        $return = $adapter->fetchPairs($select);
        return $return;
    }

    /**
     * Save notifications (if not exists)
     *
     * @param Mage_AdminNotification_Model_Inbox $object
     * @param array $data
     */
    public function parse(Mage_AdminNotification_Model_Inbox $object, array $data)
    {
        $adapter = $this->_getWriteAdapter();
        foreach ($data as $item) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('url=?', $item['url']);

            if (isset($item['internal'])) {
                $row = false;
                unset($item['internal']);
            } else {
                $row = $adapter->fetchRow($select);
            }

            if (!$row) {
                $adapter->insert($this->getMainTable(), $item);
            }
        }
    }
}
