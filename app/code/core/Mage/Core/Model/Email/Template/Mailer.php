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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Email Template Mailer Model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Email_Template_Mailer extends Varien_Object
{
    /**
     * List of email infos
     * @see Mage_Core_Model_Email_Info
     *
     * @var array
     */
    protected $_emailInfos = array();

    /**
     * Add new email info to corresponding list
     *
     * @param Mage_Core_Model_Email_Info $emailInfo
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function addEmailInfo(Mage_Core_Model_Email_Info $emailInfo)
    {
        array_push($this->_emailInfos, $emailInfo);
        return $this;
    }

    /**
     * Send all emails from email list
     * @see self::$_emailInfos
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function send()
    {
        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                $this->getTemplateId(),
                $this->getSender(),
                $emailInfo->getToEmails(),
                $emailInfo->getToNames(),
                $this->getTemplateParams(),
                $this->getStoreId()
            );
        }
        return $this;
    }

    /**
     * Set email sender
     *
     * @param string|array $sender
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function setSender($sender)
    {
        return $this->setData('sender', $sender);
    }

    /**
     * Get email sender
     *
     * @return string|array|null
     */
    public function getSender()
    {
        return $this->_getData('sender');
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Set template id
     *
     * @param int $templateId
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function setTemplateId($templateId)
    {
        return $this->setData('template_id', $templateId);
    }

    /**
     * Get template id
     *
     * @return int|null
     */
    public function getTemplateId()
    {
        return $this->_getData('template_id');
    }

    /**
     * Set tempate parameters
     *
     * @param array $templateParams
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    public function setTemplateParams(array $templateParams)
    {
        return $this->setData('template_params', $templateParams);
    }

    /**
     * Get template parameters
     *
     * @return array|null
     */
    public function getTemplateParams()
    {
        return $this->_getData('template_params');
    }
}
