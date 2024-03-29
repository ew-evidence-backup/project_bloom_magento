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
 * Wishlist Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Wishlist_Product_Collection extends Mage_Wishlist_Model_Resource_Product_Collection
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('wishlist/wishlist');
    }

    /**
     * Add wishlist count
     *
     * @return Mage_Reports_Model_Resource_Wishlist_Product_Collection
     */
    public function addWishlistCount()
    {
        $wishlistItemTable = $this->getTable('wishlist/item');
        $this->getSelect()
            ->join(
                array('wi' => $wishlistItemTable),
                'wi.product_id = e.entity_id',
                array('wishlists' => new Zend_Db_Expr('COUNT(wi.wishlist_item_id)')))
            ->where('wi.product_id = e.entity_id')
            ->group('wi.product_id');
        /*
         * Allow Analytic Functions Usage
         */
        $this->_useAnalyticFunction = true;

        $this->getEntity()->setStore(0);
        return $this;
    }

    /**
     * add customer count to result
     *
     * @return Mage_Reports_Model_Resource_Wishlist_Product_Collection
     */
    public function getCustomerCount()
    {
        $this->getSelect()->reset();

        $this->getSelect()
            ->from(
                array('wishlist' => $this->getTable('wishlist/wishlist')),
                array(
                    'wishlist_cnt' => new Zend_Db_Expr('COUNT(wishlist.wishlist_id)'),
                    'wishlist.customer_id'
                ))
            ->group('wishlist.customer_id');
        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns("COUNT(*)");

        return $countSelect;
    }

    /**
     * Set order to result
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Reports_Model_Resource_Wishlist_Product_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if ($attribute == 'wishlists') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}

