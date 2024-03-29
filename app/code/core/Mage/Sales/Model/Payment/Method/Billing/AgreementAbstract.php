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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Sales Billing Agreement Payment Method Abstract model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Transport billing agreement id
     *
     */
    const TRANSPORT_BILLING_AGREEMENT_ID = 'ba_agreement_id';
    const PAYMENT_INFO_REFERENCE_ID      = 'ba_reference_id';

    protected $_infoBlockType = 'sales/payment_info_billing_agreement';
    protected $_formBlockType = 'sales/payment_form_billing_agreement';

    /**
     * Is method instance available
     *
     * @var null|bool
     */
    protected $_isAvailable = null;

    /**
     * Check whether method is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (is_null($this->_isAvailable)) {
            if (is_object($quote) && $quote->getCustomer()) {
                $availableBA = Mage::getModel('sales/billing_agreement')->getAvailableCustomerBillingAgreements(
                    $quote->getCustomer()->getId()
                );
                $isAvailableBA = count($availableBA) > 0;
                $this->_canUseCheckout = $this->_canUseInternal = $isAvailableBA;
            }
            $this->_isAvailable = parent::isAvailable($quote) && $this->_isAvailable($quote);
            $this->_canUseCheckout = ($this->_isAvailable && $this->_canUseCheckout);
            $this->_canUseInternal = ($this->_isAvailable && $this->_canUseInternal);
        }
        return $this->_isAvailable;
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $result = parent::assignData($data);

        $key = self::TRANSPORT_BILLING_AGREEMENT_ID;
        $id = false;
        if (is_array($data) && isset($data[$key])) {
            $id = $data[$key];
        } elseif ($data instanceof Varien_Object && $data->getData($key)) {
            $id = $data->getData($key);
        }
        if ($id) {
            $info = $this->getInfoInstance();
            $ba = Mage::getModel('sales/billing_agreement')->load($id);
            if ($ba->getId() && $ba->getCustomerId() == $info->getQuote()->getCustomer()->getId()) {
                $info->setAdditionalInformation($key, $id)
                    ->setAdditionalInformation(self::PAYMENT_INFO_REFERENCE_ID, $ba->getReferenceId());
            }
        }
        return $result;
    }

    /**
     *
     *
     * @param unknown_type $quote
     */
    abstract protected function _isAvailable($quote);
}
