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
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Wishlist block shared items
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Share_Wishlist extends Mage_Wishlist_Block_Abstract
{
    /**
     * Customer instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Prepare global layout
     *
     * @return Mage_Wishlist_Block_Share_Wishlist
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->getHeader());
        }
        return $this;
    }

    /**
     * Retrieve Shared Wishlist Customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getWishlistCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')
                ->load($this->_getWishlist()->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Retrieve Page Header
     *
     * @return string
     */
    public function getHeader()
    {
        return Mage::helper('wishlist')->__("%s's Wishlist", $this->escapeHtml($this->getWishlistCustomer()->getFirstname()));
    }
}
