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
 * PayPal Standard payment "form"
 */
class Mage_Paypal_Block_Standard_Form extends Mage_Payment_Block_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Mage_Paypal_Model_Config::METHOD_WPS;

    /**
     * Config model instance
     *
     * @var Mage_Paypal_Model_Config
     */
    protected $_config;

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $this->_config = Mage::getModel('paypal/config')->setMethod($this->getMethodCode());
        $locale = Mage::app()->getLocale();
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('paypal/payment/mark.phtml')
            ->setPaymentAcceptanceMarkHref($this->_config->getPaymentMarkWhatIsPaypalUrl($locale))
            ->setPaymentAcceptanceMarkSrc($this->_config->getPaymentMarkImageUrl($locale->getLocaleCode()))
        ; // known issue: code above will render only static mark image
        $this->setTemplate('paypal/payment/redirect.phtml')
            ->setRedirectMessage(
                Mage::helper('paypal')->__('You will be redirected to the PayPal website when you place an order.')
            )
            ->setMethodTitle('') // Output PayPal mark, omit title
            ->setMethodLabelAfterHtml($mark->toHtml())
        ;
        return parent::_construct();
    }

    /**
     * Payment method code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}
