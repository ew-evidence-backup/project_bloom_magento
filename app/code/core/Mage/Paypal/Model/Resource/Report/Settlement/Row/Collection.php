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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Resource collection for report rows
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initializing
     *
     */
    protected function _construct()
    {
        $this->_init('paypal/report_settlement_row');
    }

    /**
     * Join reports info table
     *
     * @return Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                array('report' => $this->getTable('paypal/settlement_report')),
                'report.report_id = main_table.report_id',
                array('report.account_id', 'report.report_date')
            );
        return $this;
    }

    /**
     * Filter items collection by account ID
     *
     * @param string $accountId
     * @return Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection
     */
    public function addAccountFilter($accountId)
    {
        $this->getSelect()->where('report.account_id = ?', $accountId);
        return $this;
    }
}
