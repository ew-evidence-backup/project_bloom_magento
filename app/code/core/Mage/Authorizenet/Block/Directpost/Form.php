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
 * @package     Mage_Authorizenet
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * DirectPost form block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Block_Directpost_Form extends Mage_Payment_Block_Form_Cc
{
    /**
     * Internal constructor
     * Set info template for payment step
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('authorizenet/directpost/info.phtml');
    }

    /**
     * Render block HTML
     * If method is not directpost - nothing to return
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getMethod()->getCode() != Mage::getSingleton('authorizenet/directpost')->getCode()) {
            return null;
        }

        return parent::_toHtml();
    }

    /**
     * Set method info
     *
     * @return Mage_Authorizenet_Block_Directpost_Form
     */
    public function setMethodInfo()
    {
        $payment = Mage::getSingleton('checkout/type_onepage')
            ->getQuote()
            ->getPayment();
        $this->setMethod($payment->getMethodInstance());

        return $this;
    }

    /**
     * Get type of request
     *
     * @return bool
     */
    public function isAjaxRequest()
    {
        return $this->getAction()
            ->getRequest()
            ->getParam('isAjax');
    }
}
