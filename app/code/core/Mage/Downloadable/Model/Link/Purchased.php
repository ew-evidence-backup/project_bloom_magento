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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Downloadable links purchased model
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased getResource()
 * @method int getOrderId()
 * @method Mage_Downloadable_Model_Link_Purchased setOrderId(int $value)
 * @method string getOrderIncrementId()
 * @method Mage_Downloadable_Model_Link_Purchased setOrderIncrementId(string $value)
 * @method int getOrderItemId()
 * @method Mage_Downloadable_Model_Link_Purchased setOrderItemId(int $value)
 * @method string getCreatedAt()
 * @method Mage_Downloadable_Model_Link_Purchased setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Downloadable_Model_Link_Purchased setUpdatedAt(string $value)
 * @method int getCustomerId()
 * @method Mage_Downloadable_Model_Link_Purchased setCustomerId(int $value)
 * @method string getProductName()
 * @method Mage_Downloadable_Model_Link_Purchased setProductName(string $value)
 * @method string getProductSku()
 * @method Mage_Downloadable_Model_Link_Purchased setProductSku(string $value)
 * @method string getLinkSectionTitle()
 * @method Mage_Downloadable_Model_Link_Purchased setLinkSectionTitle(string $value)
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Link_Purchased extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased');
        parent::_construct();
    }

    /**
     * Check order id
     *
     * @return Mage_Core_Model_Abstract
     */
    public function _beforeSave()
    {
        if (null == $this->getOrderId()) {
            throw new Exception(
                Mage::helper('downloadable')->__('Order id cannot be null'));
        }
        return parent::_beforeSave();
    }

}
