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
 * Links block
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Position in link list
     * @var int
     */
    protected $_position = 30;

    /**
     * Set link title, label and url
     */
    public function __construct()
    {
        parent::__construct();
        $this->initLinkProperties();
    }

    /**
     * Define label, title and url for wishlist link
     */
    public function initLinkProperties()
    {
        if ($this->helper('wishlist')->isAllow()) {
            $count = $this->getItemCount() ? $this->getItemCount() : $this->helper('wishlist')->getItemCount();
            if ($count > 1) {
                $text = $this->__('My Wishlist (%d items)', $count);
            } else if ($count == 1) {
                $text = $this->__('My Wishlist (%d item)', $count);
            } else {
                $text = $this->__('My Wishlist');
            }
            $this->_label = $text;
            $this->_title = $text;
            $this->_url = $this->getUrl('wishlist');
        }
    }

    /**
     * @deprecated after 1.4.2.0
     * @see Mage_Wishlist_Block_Links::__construct
     *
     * @return array
     */
    public function addWishlistLink()
    {
        return $this;
    }
}
