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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Mage_Tax_Model_System_Config_Source_Algorithm
{
    protected $_options;

    public function __construct()
    {
        $this->_options = array(
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_UNIT_BASE,
                'label' => Mage::helper('tax')->__('Unit Price')
            ),
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_ROW_BASE,
                'label' => Mage::helper('tax')->__('Row Total')
            ),
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_TOTAL_BASE,
                'label' => Mage::helper('tax')->__('Total')
            ),
        );
    }

    public function toOptionArray()
    {
        return $this->_options;
    }
}
