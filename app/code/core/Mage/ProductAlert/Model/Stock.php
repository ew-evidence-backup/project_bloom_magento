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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * ProductAlert for back in stock model
 *
 * @method Mage_ProductAlert_Model_Resource_Stock _getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock getResource()
 * @method int getCustomerId()
 * @method Mage_ProductAlert_Model_Stock setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_ProductAlert_Model_Stock setProductId(int $value)
 * @method int getWebsiteId()
 * @method Mage_ProductAlert_Model_Stock setWebsiteId(int $value)
 * @method string getAddDate()
 * @method Mage_ProductAlert_Model_Stock setAddDate(string $value)
 * @method string getSendDate()
 * @method Mage_ProductAlert_Model_Stock setSendDate(string $value)
 * @method int getSendCount()
 * @method Mage_ProductAlert_Model_Stock setSendCount(int $value)
 * @method int getStatus()
 * @method Mage_ProductAlert_Model_Stock setStatus(int $value)
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Stock extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('productalert/stock');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('productalert/stock_customer_collection');
    }

    public function loadByParam()
    {
        if (!is_null($this->getProductId()) && !is_null($this->getCustomerId()) && !is_null($this->getWebsiteId())) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
