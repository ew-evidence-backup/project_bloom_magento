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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Order history tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_History
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/view/tab/history.phtml');
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Compose and get order full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     * @return array
     */
    public function getFullHistory()
    {
        $order = $this->getOrder();

        $history = array();
        foreach ($order->getAllStatusHistory() as $orderComment){
            $history[$orderComment->getEntityId()] = $this->_prepareHistoryItem(
                $orderComment->getStatusLabel(),
                $orderComment->getIsCustomerNotified(),
                $orderComment->getCreatedAtDate(),
                $orderComment->getComment()
            );
        }

        foreach ($order->getCreditmemosCollection() as $_memo){
            $history[$_memo->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Credit memo #%s created', $_memo->getIncrementId()),
                    $_memo->getEmailSent(), $_memo->getCreatedAtDate());

            foreach ($_memo->getCommentsCollection() as $_comment){
                $history[$_comment->getEntityId()] =
                    $this->_prepareHistoryItem($this->__('Credit memo #%s comment added', $_memo->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAtDate(), $_comment->getComment());
            }
        }

        foreach ($order->getShipmentsCollection() as $_shipment){
            $history[$_shipment->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Shipment #%s created', $_shipment->getIncrementId()),
                    $_shipment->getEmailSent(), $_shipment->getCreatedAtDate());

            foreach ($_shipment->getCommentsCollection() as $_comment){
                $history[$_comment->getEntityId()] =
                    $this->_prepareHistoryItem($this->__('Shipment #%s comment added', $_shipment->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAtDate(), $_comment->getComment());
            }
        }

        foreach ($order->getInvoiceCollection() as $_invoice){
            $history[$_invoice->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Invoice #%s created', $_invoice->getIncrementId()),
                    $_invoice->getEmailSent(), $_invoice->getCreatedAtDate());

            foreach ($_invoice->getCommentsCollection() as $_comment){
                $history[$_comment->getEntityId()] =
                    $this->_prepareHistoryItem($this->__('Invoice #%s comment added', $_invoice->getIncrementId()),
                        $_comment->getIsCustomerNotified(), $_comment->getCreatedAtDate(), $_comment->getComment());
            }
        }

        foreach ($order->getTracksCollection() as $_track){
            $history[$_track->getEntityId()] =
                $this->_prepareHistoryItem($this->__('Tracking number %s for %s assigned', $_track->getNumber(), $_track->getTitle()),
                    false, $_track->getCreatedAtDate());
        }

        krsort($history);
        return $history;
    }

    /**
     * Status history date/datetime getter
     * @param array $item
     * @return string
     */
    public function getItemCreatedAt(array $item, $dateType = 'date', $format = 'medium')
    {
        if (!isset($item['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($item['created_at'], $format);
        }
        return $this->helper('core')->formatTime($item['created_at'], $format);
    }

    /**
     * Status history item title getter
     * @param array $item
     * @return string
     */
    public function getItemTitle(array $item)
    {
        return (isset($item['title']) ? $this->escapeHtml($item['title']) : '');
    }

    /**
     * Check whether status history comment is with customer notification
     * @param array $item
     * @return bool
     */
    public function isItemNotified(array $item, $isSimpleCheck = true)
    {
        if ($isSimpleCheck) {
            return !empty($item['notified']);
        }
        return isset($item['notified']) && false !== $item['notified'];
    }

    /**
     * Status history item comment getter
     * @param array $item
     * @return string
     */
    public function getItemComment(array $item)
    {
        $allowedTags = array('b','br','strong','i','u');
        return (isset($item['comment']) ? $this->escapeHtml($item['comment'], $allowedTags) : '');
    }

    /**
     * Map history items as array
     * @param string $label
     * @param bool $notified
     * @param Zend_Date $created
     * @param string $comment
     */
    protected function _prepareHistoryItem($label, $notified, $created, $comment = '')
    {
        return array(
            'title'      => $label,
            'notified'   => $notified,
            'comment'    => $comment,
            'created_at' => $created
        );
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Comments History');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Order History');
    }

    public function getTabClass()
    {
        return 'ajax only';
    }

    public function getClass()
    {
        return $this->getTabClass();
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/commentsHistory', array('_current' => true));
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param array $history
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] == Mage_Sales_Model_Order_Status_History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }
}
