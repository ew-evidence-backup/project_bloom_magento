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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Queue resource collection
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Queue_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/queue');
    }

    /**
     * Initialize collection select
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinNames();
        return $this;
    }

    /**
     * Join Template Name and Application Name to collection
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    protected function _joinNames()
    {
        $this->_joinTemplateName();
        $this->_joinApplicationName();
        return $this;
    }

   /**
    * Join Template Name to collection
    *
    * @return Mage_XmlConnect_Model_Resource_Queue_Collection
    */
    protected function _joinTemplateName()
    {
        $this->getSelect()->joinLeft(
            array('t' => $this->getTable('xmlconnect/template')),
            't.template_id = main_table.template_id',
            array('template_name' => 't.name')
        );
        return $this;
    }

    /**
     * Join Application Name to collection
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    protected function _joinApplicationName()
    {
        $this->getSelect()->joinLeft(
            array('app' => $this->getTable('xmlconnect/application')),
            'app.application_id = t.application_id',
            array('application_name' => 'app.name')
        );
        return $this;
    }

    /**
     * Add filter by only ready fot sending item
     *
     * @return Mage_XmlConnect_Model_Resource_Queue_Collection
     */
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()->where('main_table.status in (?)', array(Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE))
             ->where('main_table.exec_time < ?', Mage::getSingleton('core/date')->gmtDate())
             ->order(new Zend_Db_Expr('main_table.exec_time ' . Zend_Db_Select::SQL_ASC)
        );
        return $this;
    }
}
