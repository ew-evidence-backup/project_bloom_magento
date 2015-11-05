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
 * DataFlow Session Model
 *
 * @method Mage_Dataflow_Model_Resource_Session _getResource()
 * @method Mage_Dataflow_Model_Resource_Session getResource()
 * @method int getUserId()
 * @method Mage_Dataflow_Model_Session setUserId(int $value)
 * @method string getCreatedDate()
 * @method Mage_Dataflow_Model_Session setCreatedDate(string $value)
 * @method string getFile()
 * @method Mage_Dataflow_Model_Session setFile(string $value)
 * @method string getType()
 * @method Mage_Dataflow_Model_Session setType(string $value)
 * @method string getDirection()
 * @method Mage_Dataflow_Model_Session setDirection(string $value)
 * @method string getComment()
 * @method Mage_Dataflow_Model_Session setComment(string $value)
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Session extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('dataflow/session');
    }

}
