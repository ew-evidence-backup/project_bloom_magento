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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


class Mage_Eav_Model_Convert_Adapter_Grid
    extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    protected $_entity;

    public function getEntity()
    {
        if (!$this->_entityType) {
            if (!($entityType = $this->getVar('entity_type'))
                || !(($entity = Mage::getResourceSingleton($entityType)) instanceof Mage_Eav_Model_Entity_Interface)) {
                $this->addException(Mage::helper('eav')->__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
            }
            $this->_entity = $entity;
        }
        return $this->_entity;
    }

    public function load()
    {
        try {
            $collection = Mage::getResourceModel($this->getEntity().'_collection');
            $collection->load();
        } catch (Exception $e) {
            $this->addException(Mage::helper('eav')->__('An error occurred while loading the collection, aborting. Error: %s', $e->getMessage()), Varien_Convert_Exception::FATAL);
        }

        $data = array();
        foreach ($collection->getIterator() as $entity) {
            $data[] = $entity->getData();
        }
        $this->setData($data);
        return $this;
    }

    public function save()
    {
        foreach ($this->getData() as $i=>$row) {
            $this->setExceptionLocation('Line: '.$i);
            $entity = Mage::getResourceModel($this->getEntity());
            if (!empty($row['entity_id'])) {
                try {
                    $entity->load($row['entity_id']);
                    $this->setPosition('Line: '.$i.(isset($row['entity_id']) ? ', entity_id: '.$row['entity_id'] : ''));
                } catch (Exception $e) {
                    $this->addException(Mage::helper('eav')->__('An error occurred while loading a record, aborting. Error: %s', $e->getMessage()), Varien_Convert_Exception::FATAL);
                }
                if (!$entity->getId()) {
                    $this->addException(Mage::helper('eav')->__('Invalid entity_id, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }
            }
            try {
                $entity->addData($row)->save();
            } catch (Exception $e) {
                $this->addException(Mage::helper('eav')->__('An error occurred while saving a record, aborting. Error: ', $e->getMessage()), Varien_Convert_Exception::FATAL);
            }
        }
        return $this;
    }
}
