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
 * @package     Mage_CatalogRule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Catalog Rule Product Aggregated Price per date Model
 *
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price _getResource()
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price getResource()
 * @method string getRuleDate()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setRuleDate(string $value)
 * @method int getCustomerGroupId()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setCustomerGroupId(int $value)
 * @method int getProductId()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setProductId(int $value)
 * @method float getRulePrice()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setRulePrice(float $value)
 * @method int getWebsiteId()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setWebsiteId(int $value)
 * @method string getLatestStartDate()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setLatestStartDate(string $value)
 * @method string getEarliestEndDate()
 * @method Mage_CatalogRule_Model_Rule_Product_Price setEarliestEndDate(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Rule_Product_Price extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogrule/rule_product_price');
    }

    /**
     * Apply price rule price to price index table
     *
     * @param Varien_Db_Select $select
     * @param array|string $indexTable
     * @param string $entityId
     * @param string $customerGroupId
     * @param string $websiteId
     * @param array $updateFields       the array fields for compare with rule price and update
     * @param string $websiteDate
     * @return Mage_CatalogRule_Model_Rule_Product_Price
     */
    public function applyPriceRuleToIndexTable(Varien_Db_Select $select, $indexTable, $entityId, $customerGroupId,
        $websiteId, $updateFields, $websiteDate)
    {

        $this->_getResource()->applyPriceRuleToIndexTable($select, $indexTable, $entityId, $customerGroupId, $websiteId,
            $updateFields, $websiteDate);

        return $this;
    }
}
