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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Rule report resource model
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Report_Rule extends Mage_Reports_Model_Resource_Report_Abstract
{
    /**
     * Resource Report Rule constructor
     *
     */
    protected function _construct()
    {
        $this->_setResource('salesrule');
    }

    /**
     * Aggregate Coupons data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Resource_Report_Rule
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('salesrule/report_rule_createdat')->aggregate($from, $to);
        Mage::getResourceModel('salesrule/report_rule_updatedat')->aggregate($from, $to);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE);

        return $this;
    }

    /**
     * Aggregate coupons reports by order created at as range
     *
     * @deprecated after 1.6.0.0-rc2
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_SalesRule_Model_Resource_Report_Rule
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        Mage::getResourceModel('salesrule/report_rule_createdat')->aggregate($from, $to);
        return $this;
    }
}
