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
 * @package     Mage_Log
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Log Prepare Online visitors resource 
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Resource_Visitor_Online extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function _construct()
    {
        $this->_init('log/visitor_online', 'visitor_id');
    }

    /**
     * Prepare online visitors for collection
     *
     * @param Mage_Log_Model_Visitor_Online $object
     * @return Mage_Log_Model_Resource_Visitor_Online
     */
    public function prepare(Mage_Log_Model_Visitor_Online $object)
    {
        if (($object->getUpdateFrequency() + $object->getPrepareAt()) > time()) {
            return $this;
        }

        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $writeAdapter->beginTransaction();

        try{
            $writeAdapter->delete($this->getMainTable());

            $visitors = array();
            $lastUrls = array();

            // retrieve online visitors general data

            $lastDate = Mage::getModel('core/date')->gmtTimestamp() - $object->getOnlineInterval() * 60;

            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/visitor'),
                    array('visitor_id', 'first_visit_at', 'last_visit_at', 'last_url_id'))
                ->where('last_visit_at >= ?', $readAdapter->formatDate($lastDate));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']] = $row;
                $lastUrls[$row['last_url_id']] = $row['visitor_id'];
                $visitors[$row['visitor_id']]['visitor_type'] = Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR;
                $visitors[$row['visitor_id']]['customer_id']  = null;
            }

            if (!$visitors) {
                $this->commit();
                return $this;
            }

            // retrieve visitor remote addr
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/visitor_info'),
                    array('visitor_id', 'remote_addr'))
                ->where('visitor_id IN(?)', array_keys($visitors));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']]['remote_addr'] = $row['remote_addr'];
            }

            // retrieve visitor last URLs
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/url_info_table'),
                    array('url_id', 'url'))
                ->where('url_id IN(?)', array_keys($lastUrls));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitorId = $lastUrls[$row['url_id']];
                $visitors[$visitorId]['last_url'] = $row['url'];
            }

            // retrieve customers
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/customer'),
                    array('visitor_id', 'customer_id'))
                ->where('visitor_id IN(?)', array_keys($visitors));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']]['visitor_type'] = Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER;
                $visitors[$row['visitor_id']]['customer_id']  = $row['customer_id'];
            }

            foreach ($visitors as $visitorData) {
                unset($visitorData['last_url_id']);

                $writeAdapter->insertForce($this->getMainTable(), $visitorData);
            }

            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $object->setPrepareAt();

        return $this;
    }
}
