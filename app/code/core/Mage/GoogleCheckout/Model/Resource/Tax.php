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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Tax resource model
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleCheckout_Model_Resource_Tax extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model initialization
     * Set main entity table name and primary key field name.
     *
     */
    protected function _construct()
    {
        $this->_init('tax/tax_rule', 'rule_id');
    }

    /**
     * Retrieve array of rule rates for customers tax class
     *
     * @param int $customerTaxClass
     * @return array
     */
    public function fetchRuleRatesForCustomerTaxClass($customerTaxClass)
    {
        $read   = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('rule' => $this->getMainTable()))
            ->join(
                array('rd' => $this->getTable('tax/tax_rate_data')),
                'rd.rate_type_id = rule.tax_rate_type_id',
                array('value' => new Zend_Db_Expr('rate_value/100')))
            ->join(
                array('r' => $this->getTable('tax/tax_rate')),
                'r.tax_rate_id = rd.tax_rate_id',
                array('country' => 'tax_country_id', 'postcode' => 'tax_postcode'))
            ->joinLeft(
                array('reg' => $this->getTable('directory/country_region')),
                'reg.region_id = r.tax_region_id',
                array('state' => 'code'))
            ->where('rule.tax_customer_class_id = ?', (int)$customerTaxClass);

        $rows = $read->fetchAll($select);

        return $rows;
    }
}
