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
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Catalog data index model for giftcards
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftCard_Model_Resource_Catalogindex_Data_Giftcard
    extends Mage_CatalogIndex_Model_Resource_Data_Abstract
{
    /**
     * Amounts cache
     *
     * @var array
     */
    protected $_cache  = array();

    /**
     * Get amounts by product and store
     *
     * @param int $product
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getAmounts($product, $store)
    {
        $isGlobal = ($store->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE)
                == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);

        if ($isGlobal) {
            $key = $product;
        } else {
            $website = $store->getWebsiteId();
            $key = "{$product}|{$website}";
        }

        $read = $this->_getReadAdapter();
        if (!isset($this->_cache[$key])) {
            $select = $read->select()
                ->from($this->getTable('enterprise_giftcard/amount'), array('value', 'website_id'))
                ->where('entity_id = ?', $product);
            $bind = array(
                'product_id' => $product
            );
            if ($isGlobal) {
                $select->where('website_id = 0');
            } else {
                $select->where('website_id IN (0, :website_id)');
                $bind['website_id'] = $website;
            }
            $fetched = $read->fetchAll($select, $bind);
            $this->_cache[$key] = $this->_convertPrices($fetched, $store);
        }
        return $this->_cache[$key];
    }

    /**
     * Convert amounts to store base currency
     *
     * @param array $amounts
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    protected function _convertPrices($amounts, $store)
    {
        $result = array();
        if (is_array($amounts) && $amounts) {
            foreach ($amounts as $amount) {
                $value = $amount['value'];
                if ($amount['website_id'] == 0) {
                    $rate = $store->getBaseCurrency()->getRate(Mage::app()->getBaseCurrencyCode());
                    if ($rate) {
                        $value = $value / $rate;
                    } else {
                        continue;
                    }
                }
                $result[] = $value;
            }
        }
        return $result;
    }
}
