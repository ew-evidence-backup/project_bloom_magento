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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Bundle Selection Model
 *
 * @method Mage_Bundle_Model_Resource_Selection _getResource()
 * @method Mage_Bundle_Model_Resource_Selection getResource()
 * @method int getOptionId()
 * @method Mage_Bundle_Model_Selection setOptionId(int $value)
 * @method int getParentProductId()
 * @method Mage_Bundle_Model_Selection setParentProductId(int $value)
 * @method int getProductId()
 * @method Mage_Bundle_Model_Selection setProductId(int $value)
 * @method int getPosition()
 * @method Mage_Bundle_Model_Selection setPosition(int $value)
 * @method int getIsDefault()
 * @method Mage_Bundle_Model_Selection setIsDefault(int $value)
 * @method int getSelectionPriceType()
 * @method Mage_Bundle_Model_Selection setSelectionPriceType(int $value)
 * @method float getSelectionPriceValue()
 * @method Mage_Bundle_Model_Selection setSelectionPriceValue(float $value)
 * @method float getSelectionQty()
 * @method Mage_Bundle_Model_Selection setSelectionQty(float $value)
 * @method int getSelectionCanChangeQty()
 * @method Mage_Bundle_Model_Selection setSelectionCanChangeQty(int $value)
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Selection extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('bundle/selection');
        parent::_construct();
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Bundle_Model_Selection
     */
    protected function _beforeSave()
    {
        $storeId = Mage::registry('product')->getStoreId();
        if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
            $this->setWebsiteId(Mage::app()->getStore($storeId)->getWebsiteId());
            $this->getResource()->saveSelectionPrice($this);

            if (!$this->getDefaultPriceScope()) {
                $this->unsSelectionPriceValue();
                $this->unsSelectionPriceType();
            }
        }
        parent::_beforeSave();
    }
}
