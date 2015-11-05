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
 * @package     Mage_Dataflow
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Dataflow Batch abstract resource model
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Dataflow_Model_Resource_Batch_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Retrieve Id collection
     *
     * @param Mage_Dataflow_Model_Batch_Abstract $object
     * @return array
     */
    public function getIdCollection(Mage_Dataflow_Model_Batch_Abstract $object)
    {
        if (!$object->getBatchId()) {
            return array();
        }

        $ids = array();
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('batch_id = :batch_id');
        $ids = $this->_getWriteAdapter()->fetchCol($select, array('batch_id' => $object->getBatchId()));
        return $ids;
    }

    /**
     * Delete current Batch collection
     *
     * @param Mage_Dataflow_Model_Batch_Abstract $object
     * @return Mage_Dataflow_Model_Resource_Batch_Abstract
     */
    public function deleteCollection(Mage_Dataflow_Model_Batch_Abstract $object)
    {
        if (!$object->getBatchId()) {
            return $this;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), array('batch_id=?' => $object->getBatchId()));
        return $this;
    }
}
