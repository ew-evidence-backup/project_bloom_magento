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
 * Convert zend cache adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Adapter_Zend_Cache extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Cache::factory($this->getVar('frontend', 'Core'), $this->getVar('backend', 'File'));
        }
        return $this->_resource;
    }

    public function load()
    {
        $this->setData($this->getResource()->load($this->getVar('id')));
        return $this;
    }

    public function save()
    {
        $this->getResource()->save($this->getData(), $this->getVar('id'));
        return $this;
    }

}