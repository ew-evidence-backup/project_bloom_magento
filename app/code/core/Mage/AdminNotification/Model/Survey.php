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
 * @package     Mage_AdminNotification
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * AdminNotification survey model
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Survey
{
    protected static $_flagCode  = 'admin_notification_survey';
    protected static $_flagModel = null;

    const SURVEY_URL = 'www.magentocommerce.com/instsurvey.html';

    /**
     * Check if survey url valid (exists) or not
     *
     * @return boolen
     */
    public static function isSurveyUrlValid()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array('timeout'   => 5))
            ->write(Zend_Http_Client::GET, self::getSurveyUrl(), '1.0');
        $response = $curl->read();
        if (Zend_Http_Response::extractCode($response) == 200) {
            return true;
        }
        return false;
    }

    /**
     * Return survey url
     *
     * @return string
     */
    public static function getSurveyUrl()
    {
        $host = Mage::app()->getRequest()->isSecure() ? 'https://' : 'http://';
        return $host . self::SURVEY_URL;
    }

    /**
     * Return core flag model
     *
     * @return Mage_Core_Model_Flag
     */
    protected static function _getFlagModel()
    {
        if (self::$_flagModel === null) {
            self::$_flagModel = Mage::getModel('core/flag', array('flag_code' => self::$_flagCode))->loadSelf();
        }
        return self::$_flagModel;
    }

    /**
     * Check if survey question was already asked
     * or survey url was viewed during installation process
     *
     * @return boolean
     */
    public static function isSurveyViewed()
    {
        $flagData = self::_getFlagModel()->getFlagData();
        if (isset($flagData['survey_viewed']) && $flagData['survey_viewed'] == 1) {
            return true;
        }
        return false;
    }

    /**
     * Save survey viewed flag in core flag
     *
     * @param boolean $viewed
     */
    public static function saveSurveyViewed($viewed)
    {
        $flagData = self::_getFlagModel()->getFlagData();
        if (is_null($flagData)) {
            $flagData = array();
        }
        $flagData = array_merge($flagData, array('survey_viewed' => (bool)$viewed));
        self::_getFlagModel()->setFlagData($flagData)->save();
    }
}
