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
 * @package     Mage_Review
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Product Reviews Page
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    protected $_reviewsCollection;

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getProduct()->setShortDescription(null);

        return parent::_toHtml();
    }

    /**
     * Replace review summary html with more detailed review summary
     * Reviews collection count will be jerked here
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
    {
        return
            $this->getLayout()->createBlock('rating/entity_detailed')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml()
            .
            $this->getLayout()->getBlock('product_review_list.count')
                ->assign('count', $this->getReviewsCollection()->getSize())
                ->toHtml()
            ;
    }

    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    /**
     * Force product view page behave like without options
     *
     * @return false
     */
    public function hasOptions()
    {
        return false;
    }
}
