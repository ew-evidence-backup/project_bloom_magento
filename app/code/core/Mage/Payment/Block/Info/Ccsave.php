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
 * @package     Mage_Payment
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Mage_Payment_Block_Info_Ccsave extends Mage_Payment_Block_Info_Cc
{
    /**
     * Show name on card, expiration date and full cc number
     *
     * Expiration date and full number will show up only in secure mode (only for admin, not in emails or pdfs)
     *
     * @param Varien_Object|array $transport
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object(array(Mage::helper('payment')->__('Name on the Card') => $info->getCcOwner(),));
        $transport = parent::_prepareSpecificInformation($transport);
        if (!$this->getIsSecureMode()) {
            $transport->addData(array(
                Mage::helper('payment')->__('Expiration Date') => $this->_formatCardDate(
                    $info->getCcExpYear(), $this->getCcExpMonth()
                ),
                Mage::helper('payment')->__('Credit Card Number') => $info->getCcNumber(),
            ));
        }
        return $transport;
    }
}
