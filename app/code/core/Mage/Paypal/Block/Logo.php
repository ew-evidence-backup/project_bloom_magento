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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * PayPal online logo with additional options
 */
class Mage_Paypal_Block_Logo extends Mage_Core_Block_Template
{
    /**
     * Return URL for Paypal Landing page
     *
     * @return string
     */
    public function getAboutPaypalPageUrl()
    {
        return $this->_getConfig()->getPaymentMarkWhatIsPaypalUrl(Mage::app()->getLocale());
    }

    /**
     * Getter for paypal config
     *
     * @return Mage_Paypal_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('paypal/config');
    }

    /**
     * Disable block output if logo turned off
     *
     * @return string
     */
    protected function _toHtml()
    {
        $type = $this->getLogoType(); // assigned in layout etc.
        $logoUrl = $this->_getConfig()->getAdditionalOptionsLogoUrl(Mage::app()->getLocale()->getLocaleCode(), $type);
        if (!$logoUrl) {
            return '';
        }
        $this->setLogoImageUrl($logoUrl);
        return parent::_toHtml();
    }
}
