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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Reports Recently Viewed Products Block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Block_Product_Viewed extends Mage_Reports_Block_Product_Abstract
{
    const XML_PATH_RECENTLY_VIEWED_COUNT    = 'catalog/recently_products/viewed_count';

    /**
     * Viewed Product Index model name
     *
     * @var string
     */
    protected $_indexName       = 'reports/product_index_viewed';

    /**
     * Retrieve page size (count)
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }
        return Mage::getStoreConfig(self::XML_PATH_RECENTLY_VIEWED_COUNT);
    }

    /**
     * Added predefined ids support
     */
    public function getCount()
    {
        $ids = $this->getProductIds();
        if (!empty($ids)) {
            return count($ids);
        }
        return parent::getCount();
    }

    /**
     * Prepare to html
     * check has viewed products
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getCount()) {
            return '';
        }
        $this->setRecentlyViewedProducts($this->getItemsCollection());
        return parent::_toHtml();
    }
}
