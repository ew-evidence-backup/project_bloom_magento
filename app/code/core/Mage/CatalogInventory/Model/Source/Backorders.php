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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Mage_CatalogInventory_Model_Source_Backorders
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_NO, 'label'=>Mage::helper('cataloginventory')->__('No Backorders')),
            array('value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY, 'label'=>Mage::helper('cataloginventory')->__('Allow Qty Below 0')),
            array('value' => Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY , 'label'=>Mage::helper('cataloginventory')->__('Allow Qty Below 0 and Notify Customer')),
        );
    }
}
