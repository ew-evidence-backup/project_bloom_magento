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
 * @package     Mage_Index
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Abstract index process class
 * Predefine list of methods required by indexer
 */
abstract class Mage_Index_Model_Indexer_Abstract extends Mage_Core_Model_Abstract
{
    protected $_matchedEntities = array();

    /**
     * Whether table changes are allowed
     *
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Get Indexer name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get Indexer description
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Register indexer required data inside event object
     *
     * @param   Mage_Index_Model_Event $event
     */
    abstract protected function _registerEvent(Mage_Index_Model_Event $event);

    /**
     * Process event based on event state data
     *
     * @param   Mage_Index_Model_Event $event
     */
    abstract protected function _processEvent(Mage_Index_Model_Event $event);

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    public function register(Mage_Index_Model_Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_registerEvent($event);
        }
        return $this;
    }

    /**
     * Process event
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Index_Model_Indexer_Abstract
     */
    public function processEvent(Mage_Index_Model_Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_processEvent($event);
        }
        return $this;
    }

    /**
     * Check if event can be matched by process
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();
        $type   = $event->getType();
        return $this->matchEntityAndType($entity, $type);
    }

    /**
     * Check if indexer matched specific entity and action type
     *
     * @param   string $entity
     * @param   string $type
     * @return  bool
     */
    public function matchEntityAndType($entity, $type)
    {
        if (isset($this->_matchedEntities[$entity])) {
            if (in_array($type, $this->_matchedEntities[$entity])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        $this->_getResource()->reindexAll();
    }

    /**
     * Try dynamicly detect and call event hanler from resource model.
     * Handler name will be generated from event entity and type code
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Index_Model_Indexer_Abstract
     */
    public function callEventHandler(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity()) {
            $method = $this->_camelize($event->getEntity().'_'.$event->getType());
        } else {
            $method = $this->_camelize($event->getType());
        }

        $resourceModel = $this->_getResource();
        if (method_exists($resourceModel, $method)) {
            if (!$this->_allowTableChanges && is_callable(array($resourceModel, 'setAllowTableChanges'))) {
                $resourceModel->setAllowTableChanges(false);
            }
            $resourceModel->$method($event);
            if (!$this->_allowTableChanges && is_callable(array($resourceModel, 'setAllowTableChanges'))) {
                $resourceModel->setAllowTableChanges(true);
            }
        }
        return $this;
    }

    /**
     * Set whether table changes are allowed
     *
     * @param bool $value
     * @return Mage_Index_Model_Indexer_Abstract
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Disable resource table keys
     *
     * @return Mage_Index_Model_Indexer_Abstract
     */
    public function disableKeys()
    {
        if (empty($this->_resourceName)) {
            return $this;
        }

        $resourceModel = $this->getResource();
        if ($resourceModel instanceof Mage_Index_Model_Resource_Abstract) {
            $resourceModel->useDisableKeys(true);
            $resourceModel->disableTableKeys();
        }

        return $this;
    }

    /**
     * Enable resource table keys
     *
     * @return Mage_Index_Model_Indexer_Abstract
     */
    public function enableKeys()
    {
        if (empty($this->_resourceName)) {
            return $this;
        }

        $resourceModel = $this->getResource();
        if ($resourceModel instanceof Mage_Index_Model_Resource_Abstract) {
            $resourceModel->useDisableKeys(true);
            $resourceModel->enableTableKeys();
        }

        return $this;
    }
}
