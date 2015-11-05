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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Enter description here ...
 *
 * @method Mage_Core_Model_Resource_Layout _getResource()
 * @method Mage_Core_Model_Resource_Layout getResource()
 * @method string getHandle()
 * @method Mage_Core_Model_Layout_Data setHandle(string $value)
 * @method string getXml()
 * @method Mage_Core_Model_Layout_Data setXml(string $value)
 * @method int getSortOrder()
 * @method Mage_Core_Model_Layout_Data setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Data extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('core/layout');
    }
}
