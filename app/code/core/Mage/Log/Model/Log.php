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
 * Log Model
 *
 * @method Mage_Log_Model_Resource_Log _getResource()
 * @method Mage_Log_Model_Resource_Log getResource()
 * @method string getSessionId()
 * @method Mage_Log_Model_Log setSessionId(string $value)
 * @method string getFirstVisitAt()
 * @method Mage_Log_Model_Log setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method Mage_Log_Model_Log setLastVisitAt(string $value)
 * @method int getLastUrlId()
 * @method Mage_Log_Model_Log setLastUrlId(int $value)
 * @method int getStoreId()
 * @method Mage_Log_Model_Log setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Log extends Mage_Core_Model_Abstract
{
    const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

    /**
     * Init Resource Model
     *
     */
    protected function _construct()
    {
        $this->_init('log/log');
    }

    public function getLogCleanTime()
    {
        return Mage::getStoreConfig(self::XML_LOG_CLEAN_DAYS) * 60 * 60 * 24;
    }

    /**
     * Clean Logs
     *
     * @return Mage_Log_Model_Log
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
