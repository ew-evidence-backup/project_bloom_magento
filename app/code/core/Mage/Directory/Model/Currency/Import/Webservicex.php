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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Currency rate import model (From www.webservicex.net)
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Currency_Import_Webservicex extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_url = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency={{CURRENCY_FROM}}&ToCurrency={{CURRENCY_TO}}';
    protected $_messages = array();

     /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/webservicex/timeout')))
                ->request('GET')
                ->getBody();

            $xml = simplexml_load_string($response, null, LIBXML_NOERROR);
            if( !$xml ) {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
                return null;
            }
            return (float) $xml;
        }
        catch (Exception $e) {
            if( $retry == 0 ) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
            }
        }
    }
}
