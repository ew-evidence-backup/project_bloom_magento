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

class Mage_Adminhtml_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Toolbar
{
    /**
     * XML path of Severity icons url
     */
    const XML_SEVERITY_ICONS_URL_PATH  = 'system/adminnotification/severity_icons_url';

    /**
     * Severity icons url
     *
     * @var string
     */
    protected $_severityIconsUrl;

    /**
     * Is available flag
     *
     * @var bool
     */
    protected $_available = null;

    /**
     * Initialize block window
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setHeaderText(addslashes($this->__('Incoming Message')));
        $this->setCloseText(addslashes($this->__('close')));
        $this->setReadDetailsText(addslashes($this->__('Read details')));
        $this->setNoticeText(addslashes($this->__('NOTICE')));
        $this->setMinorText(addslashes($this->__('MINOR')));
        $this->setMajorText(addslashes($this->__('MAJOR')));
        $this->setCriticalText(addslashes($this->__('CRITICAL')));


        $this->setNoticeMessageText(addslashes($this->getLastNotice()->getTitle()));
        $this->setNoticeMessageUrl(addslashes($this->getLastNotice()->getUrl()));

        switch ($this->getLastNotice()->getSeverity()) {
            default:
            case Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE:
                $severity = 'SEVERITY_NOTICE';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR:
                $severity = 'SEVERITY_MINOR';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR:
                $severity = 'SEVERITY_MAJOR';
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL:
                $severity = 'SEVERITY_CRITICAL';
                break;
        }

        $this->setNoticeSeverity($severity);
    }

    /**
     * Can we show notification window
     *
     * @return bool
     */
    public function canShow()
    {
        if (!is_null($this->_available)) {
            return $this->_available;
        }

        if (!Mage::getSingleton('admin/session')->isFirstPageAfterLogin()) {
            $this->_available = false;
            return false;
        }

        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            $this->_available = false;
            return false;
        }

        if (!$this->_isAllowed()) {
            $this->_available = false;
            return false;
        }

        if (is_null($this->_available)) {
            $this->_available = $this->isShow();
        }
        return $this->_available;
    }


    /**
     * Return swf object url
     *
     * @return string
     */
    public function getObjectUrl()
    {
        return $this->_getHelper()->getPopupObjectUrl();
    }

    /**
     * Retrieve Last Notice object
     *
     * @return Mage_AdminNotification_Model_Inbox
     */
    public function getLastNotice()
    {
        return $this->_getHelper()->getLatestNotice();
    }

    /**
     * Retrieve severity icons url
     *
     * @return string
     */
    public function getSeverityIconsUrl()
    {
        if (is_null($this->_severityIconsUrl)) {
            $this->_severityIconsUrl =
                (Mage::app()->getFrontController()->getRequest()->isSecure() ? 'https://' : 'http://')
                . sprintf(Mage::getStoreConfig(self::XML_SEVERITY_ICONS_URL_PATH), Mage::getVersion(),
                    $this->getNoticeSeverity())
            ;
        }
        return $this->_severityIconsUrl;
    }

    /**
     * Retrieve severity text
     *
     * @return string
     */
    public function getSeverityText()
    {
        return strtolower(str_replace('SEVERITY_', '', $this->getNoticeSeverity()));
    }

    /**
     * Check if current block allowed in ACL
     *
     * @param string $resourcePath
     * @return bool
     */
    protected function _isAllowed()
    {
        if (!is_null($this->_aclResourcePath)) {
            return Mage::getSingleton('admin/session')
                ->isAllowed('admin/system/adminnotification/show_toolbar');
        }
        else {
            return true;
        }
    }
}
