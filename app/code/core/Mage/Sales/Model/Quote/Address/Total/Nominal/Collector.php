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
 * Nominal totals collector
 */
class Mage_Sales_Model_Quote_Address_Total_Nominal_Collector extends Mage_Sales_Model_Quote_Address_Total_Collector
{
    /**
     * Conf. node for nominal totals declaration
     *
     * @var string
     */
    protected $_totalsConfigNode = 'global/sales/quote/nominal_totals';

    /**
     * Custom cache key to not confuse with regular totals
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_quote_nominal_collectors';
}
