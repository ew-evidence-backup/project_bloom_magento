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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 *
 * @method Mage_Sales_Model_Resource_Order_Tax _getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax getResource()
 * @method int getOrderId()
 * @method Mage_Sales_Model_Order_Tax setOrderId(int $value)
 * @method string getCode()
 * @method Mage_Sales_Model_Order_Tax setCode(string $value)
 * @method string getTitle()
 * @method Mage_Sales_Model_Order_Tax setTitle(string $value)
 * @method float getPercent()
 * @method Mage_Sales_Model_Order_Tax setPercent(float $value)
 * @method float getAmount()
 * @method Mage_Sales_Model_Order_Tax setAmount(float $value)
 * @method int getPriority()
 * @method Mage_Sales_Model_Order_Tax setPriority(int $value)
 * @method int getPosition()
 * @method Mage_Sales_Model_Order_Tax setPosition(int $value)
 * @method float getBaseAmount()
 * @method Mage_Sales_Model_Order_Tax setBaseAmount(float $value)
 * @method int getProcess()
 * @method Mage_Sales_Model_Order_Tax setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method Mage_Sales_Model_Order_Tax setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method Mage_Sales_Model_Order_Tax setHidden(int $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Tax extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_tax');
    }
}
