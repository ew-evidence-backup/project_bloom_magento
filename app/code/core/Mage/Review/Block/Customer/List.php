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
 * Customer Reviews list block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Block_Customer_List extends Mage_Customer_Block_Account_Dashboard
{

    protected $_collection;

    protected function _construct()
    {
        $this->_collection = Mage::getModel('review/review')->getProductCollection();
        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setDateOrder();
    }

    public function count()
    {
        return $this->_collection->getSize();
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_review_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    protected function _getCollection()
    {
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }

}
