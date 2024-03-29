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

/**
 * Payment method abstract model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Payment_Model_Method_Abstract extends Varien_Object
{
    const ACTION_ORDER              = 'order';
    const ACTION_AUTHORIZE          = 'authorize';
    const ACTION_AUTHORIZE_CAPTURE  = 'authorize_capture';

    const STATUS_UNKNOWN    = 'UNKNOWN';
    const STATUS_APPROVED   = 'APPROVED';
    const STATUS_ERROR      = 'ERROR';
    const STATUS_DECLINED   = 'DECLINED';
    const STATUS_VOID       = 'VOID';
    const STATUS_SUCCESS    = 'SUCCESS';

    protected $_code;
    protected $_formBlockType = 'payment/form';
    protected $_infoBlockType = 'payment/info';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_isGateway                   = false;
    protected $_canOrder                    = false;
    protected $_canAuthorize                = false;
    protected $_canCapture                  = false;
    protected $_canCapturePartial           = false;
    protected $_canRefund                   = false;
    protected $_canRefundInvoicePartial     = false;
    protected $_canVoid                     = false;
    protected $_canUseInternal              = true;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = true;
    protected $_isInitializeNeeded          = false;
    protected $_canFetchTransactionInfo     = false;
    protected $_canReviewPayment            = false;
    protected $_canCreateBillingAgreement   = false;
    protected $_canManageRecurringProfiles  = true;
    /**
     * TODO: whether a captured transaction may be voided by this gateway
     * This may happen when amount is captured, but not settled
     * @var bool
     */
    protected $_canCancelInvoice        = false;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    public function __construct()
    {

    }

    /**
     * Check order availability
     *
     * @return bool
     */
    public function canOrder()
    {
        return $this->_canOrder;
    }

    /**
     * Check authorise availability
     *
     * @return bool
     */
    public function canAuthorize()
    {
        return $this->_canAuthorize;
    }

    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        return $this->_canCapture;
    }

    /**
     * Check partial capture availability
     *
     * @return bool
     */
    public function canCapturePartial()
    {
        return $this->_canCapturePartial;
    }

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->_canRefund;
    }

    /**
     * Check partial refund availability for invoice
     *
     * @return bool
     */
    public function canRefundPartialPerInvoice()
    {
        return $this->_canRefundInvoicePartial;
    }

    /**
     * Check void availability
     *
     * @param   Varien_Object $payment
     * @return  bool
     */
    public function canVoid(Varien_Object $payment)
    {
        return $this->_canVoid;
    }

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return $this->_canUseInternal;
    }

    /**
     * Can be used in regular checkout
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        return $this->_canUseCheckout;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return $this->_canUseForMultishipping;
    }

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return true;
    }

    /**
     * Check fetch transaction info availability
     *
     * @return bool
     */
    public function canFetchTransactionInfo()
    {
        return $this->_canFetchTransactionInfo;
    }

    /**
     * Check whether payment method instance can create billing agreements
     *
     * @return bool
     */
    public function canCreateBillingAgreement()
    {
        return $this->_canCreateBillingAgreement;
    }

    /**
     * Fetch transaction info
     *
     * @param Mage_Payment_Model_Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId)
    {
        return array();
    }

    /**
     * Retrieve payment system relation flag
     *
     * @return bool
     */
    public function isGateway()
    {
        return $this->_isGateway;
    }

    /**
     * flag if we need to run payment initialize while order place
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return $this->_isInitializeNeeded;
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        /*
        for specific country, the flag will set up as 1
        */
        if($this->getConfigData('allowspecific')==1){
            $availableCountries = explode(',', $this->getConfigData('specificcountry'));
            if(!in_array($country, $availableCountries)){
                return false;
            }

        }
        return true;
    }

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        return true;
    }

    /**
     * Check manage billing agreements availability
     *
     * @return bool
     */
    public function canManageBillingAgreements()
    {
        return ($this instanceof Mage_Payment_Model_Billing_Agreement_MethodInterface);
    }

    /**
     * Whether can manage recurring profiles
     *
     * @return bool
     */
    public function canManageRecurringProfiles()
    {
        return $this->_canManageRecurringProfiles && ($this instanceof Mage_Payment_Model_Recurring_Profile_MethodInterface);
    }

    /**
     * Retrieve model helper
     *
     * @return Mage_Payment_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('payment');
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getCode()
    {
        if (empty($this->_code)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve the payment method code.'));
        }
        return $this->_code;
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return $this->_formBlockType;
    }

    /**
     * Retrieve block type for display method information
     *
     * @return string
     */
    public function getInfoBlockType()
    {
        return $this->_infoBlockType;
    }

    /**
     * Retrieve payment iformation model object
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfoInstance()
    {
        $instance = $this->getData('info_instance');
        if (!($instance instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve the payment information object instance.'));
        }
        return $instance;
    }

    /**
     * Validate payment method information object
     *
     * @param   Varien_Object $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {
         /**
          * to validate paymene method is allowed for billing country or not
          */
         $paymentInfo = $this->getInfoInstance();
         if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
             $billingCountry = $paymentInfo->getOrder()->getBillingAddress()->getCountryId();
         } else {
             $billingCountry = $paymentInfo->getQuote()->getBillingAddress()->getCountryId();
         }
         if (!$this->canUseForCountry($billingCountry)) {
             Mage::throwException($this->_getHelper()->__('Selected payment type is not allowed for billing country.'));
         }
         return $this;
    }

    /**
     * Order
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function order(Varien_Object $payment, $amount)
    {
        if (!$this->canOrder()) {
            Mage::throwException($this->_getHelper()->__('Order action is not available.'));
        }
        return $this;
    }

    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @param float $amount
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if (!$this->canAuthorize()) {
            Mage::throwException($this->_getHelper()->__('Authorize action is not available.'));
        }
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @param float $amount
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if (!$this->canCapture()) {
            Mage::throwException($this->_getHelper()->__('Capture action is not available.'));
        }

        return $this;
    }

    /**
     * Set capture transaction ID to invoice for informational purposes
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function processInvoice($invoice, $payment)
    {
        $invoice->setTransactionId($payment->getLastTransId());
        return $this;
    }

    /**
     * Set refund transaction id to payment object for informational purposes
     * Candidate to be deprecated:
     * there can be multiple refunds per payment, thus payment.refund_transaction_id doesn't make big sense
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function processBeforeRefund($invoice, $payment)
    {
        $payment->setRefundTransactionId($invoice->getTransactionId());
        return $this;
    }

    /**
     * Refund money
     *
     * @param   Varien_Object $invoicePayment
     * @param float $amount
     * @return  Mage_Payment_Model_Abstract
     */
    //public function refund(Varien_Object $payment, $amount)
    public function refund(Varien_Object $payment, $amount)
    {

        if (!$this->canRefund()) {
            Mage::throwException($this->_getHelper()->__('Refund action is not available.'));
        }


        return $this;
    }

    /**
     * Set transaction ID into creditmemo for informational purposes
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function processCreditmemo($creditmemo, $payment)
    {
        $creditmemo->setTransactionId($payment->getLastTransId());
        return $this;
    }

    /**
     * Cancel payment (GoogleCheckout)
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        return $this;
    }

    /**
     * @deprecated after 1.4.0.0-alpha3
     * this method doesn't make sense, because invoice must not void entire authorization
     * there should be method for invoice cancellation
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function processBeforeVoid($invoice, $payment)
    {
        $payment->setVoidTransactionId($invoice->getTransactionId());
        return $this;
    }

    /**
     * Void payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
        if (!$this->canVoid($payment)) {
            Mage::throwException($this->_getHelper()->__('Void action is not available.'));
        }
        return $this;
    }

    /**
     * Whether this method can accept or deny payment
     *
     * @param Mage_Payment_Model_Info $payment
     * @param bool $soft
     * @return bool
     */
    public function canReviewPayment(Mage_Payment_Model_Info $payment)
    {
        return $this->_canReviewPayment;
    }

    /**
     * Attempt to accept a payment that us under review
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function acceptPayment(Mage_Payment_Model_Info $payment)
    {
        if (!$this->canReviewPayment($payment)) {
            Mage::throwException(Mage::helper('payment')->__('The payment review action is unavailable.'));
        }
        return false;
    }

    /**
     * Attempt to deny a payment that us under review
     *
     * @param Mage_Payment_Model_Info $payment
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function denyPayment(Mage_Payment_Model_Info $payment)
    {
        if (!$this->canReviewPayment($payment)) {
            Mage::throwException(Mage::helper('payment')->__('The payment review action is unavailable.'));
        }
        return false;
    }

    /**
     * Retrieve payment method title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getConfigData('title');
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/'.$this->getCode().'/'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (is_array($data)) {
            $this->getInfoInstance()->addData($data);
        }
        elseif ($data instanceof Varien_Object) {
            $this->getInfoInstance()->addData($data->getData());
        }
        return $this;
    }

   /**
     * Parepare info instance for save
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function prepareSave()
    {
        return $this;
    }

    /**
     * Check whether payment method can be used
     * TODO: payment method instance is not supposed to know about quote
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $checkResult = new StdClass;
        $checkResult->isAvailable = (bool)(int)$this->getConfigData('active', ($quote ? $quote->getStoreId() : null));
        Mage::dispatchEvent('payment_method_is_active', array(
            'result'          => $checkResult,
            'method_instance' => $this,
            'quote'           => $quote,
        ));

        // disable method if it cannot implement recurring profiles management and there are recurring items in quote
        if ($checkResult->isAvailable) {
            $implementsRecurring = $this->canManageRecurringProfiles();
            // the $quote->hasRecurringItems() causes big performance impact, thus it has to be called last
            if ($quote && (!$implementsRecurring) && $quote->hasRecurringItems()) {
                $checkResult->isAvailable = false;
            }
        }
        return $checkResult->isAvailable;
    }

    /**
     * Method that will be executed instead of authorize or capture
     * if flag isInitilizeNeeded set to true
     *
     * @param   string $paymentAction
     * @return  Mage_Payment_Model_Abstract
     */
    public function initialize($paymentAction, $stateObject)
    {
        return $this;
    }

    /**
     * Get config peyment action url
     * Used to universalize payment actions when processing payment place
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('payment_action');
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
        if ($this->getDebugFlag()) {
            Mage::getModel('core/log_adapter', 'payment_' . $this->getCode() . '.log')
               ->setFilterDataKeys($this->_debugReplacePrivateDataKeys)
               ->log($debugData);
        }
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->getConfigData('debug');
    }

    /**
     * Used to call debug method from not Paymant Method context
     *
     * @param mixed $debugData
     */
    public function debugData($debugData)
    {
        $this->_debug($debugData);
    }
}
