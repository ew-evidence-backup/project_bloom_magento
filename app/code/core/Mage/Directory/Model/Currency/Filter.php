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
 * Currency filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Currency_Filter implements Zend_Filter_Interface
{
    /**
     * Rate value
     *
     * @var decimal
     */
    protected $_rate;

    /**
     * Currency object
     *
     * @var Zend_Currency
     */
    protected $_currency;

    public function __construct($code, $rate=1)
    {
        $this->_currency = Mage::app()->getLocale()->currency($code);
        $this->_rate = $rate;
    }

    /**
     * Set filter rate
     *
     * @param double $rate
     */
    public function setRate($rate)
    {
        $this->_rate = $rate;
    }

    /**
     * Filter value
     *
     * @param   double $value
     * @return  string
     */
    public function filter($value)
    {
        $value = Mage::app()->getLocale()->getNumber($value);
        $value = Mage::app()->getStore()->roundPrice($this->_rate*$value);
        //$value = round($value, 2);
        $value = sprintf("%f", $value);
        return $this->_currency->toCurrency($value);
    }
}
