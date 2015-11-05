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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Enter description here ...
 *
 * @method Mage_GoogleCheckout_Model_Resource_Api_Debug _getResource()
 * @method Mage_GoogleCheckout_Model_Resource_Api_Debug getResource()
 * @method string getDir()
 * @method Mage_GoogleCheckout_Model_Api_Debug setDir(string $value)
 * @method string getUrl()
 * @method Mage_GoogleCheckout_Model_Api_Debug setUrl(string $value)
 * @method string getRequestBody()
 * @method Mage_GoogleCheckout_Model_Api_Debug setRequestBody(string $value)
 * @method string getResponseBody()
 * @method Mage_GoogleCheckout_Model_Api_Debug setResponseBody(string $value)
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleCheckout_Model_Api_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('googlecheckout/api_debug');
    }
}
