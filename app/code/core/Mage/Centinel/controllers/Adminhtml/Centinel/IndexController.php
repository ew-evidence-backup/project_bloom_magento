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
 * @package     Mage_Centinel
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Centinel Index Controller
 *
 * @category Mage
 * @package  Mage_Centinel
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_Adminhtml_Centinel_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Process validate payment data action
     *
     */
    public function validatePaymentDataAction()
    {
        $result = array();
        try {
            $paymentData = $this->getRequest()->getParam('payment');
            $validator = $this->_getValidator();
            if (!$validator) {
                throw new Exception('This payment method does not have centinel validation.');
            }
            $validator->reset();
            $this->_getPayment()->importData($paymentData);
            $result['authenticationUrl'] = $validator->getAuthenticationStartUrl();
        } catch (Mage_Core_Exception $e) {
            $result['message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['message'] = Mage::helper('centinel')->__('Validation failed.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Process autentication start action
     *
     */
    public function authenticationStartAction()
    {
        if ($validator = $this->_getValidator()) {
            Mage::register('current_centinel_validator', $validator);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Process autentication complete action
     *
     */
    public function authenticationCompleteAction()
    {
        try {
           if ($validator = $this->_getValidator()) {
                $request = $this->getRequest();

                $data = new Varien_Object();
                $data->setTransactionId($request->getParam('MD'));
                $data->setPaResPayload($request->getParam('PaRes'));

                $validator->authenticate($data);
                Mage::register('current_centinel_validator', $validator);
            }
        } catch (Exception $e) {
            Mage::register('current_centinel_validator', false);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Return payment model
     *
     * @return Mage_Sales_Model_Quote_Payment
     */
    private function _getPayment()
    {
        $model = Mage::getSingleton('adminhtml/sales_order_create');
        return $model->getQuote()->getPayment();
    }

    /**
     * Return Centinel validation model
     *
     * @return Mage_Centinel_Model_Service
     */
    private function _getValidator()
    {
        if ($this->_getPayment()->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $this->_getPayment()->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }
}

