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

$installer = $this;
/** @var $installer Mage_Sales_Model_Mysql4_Setup */

$tableSalesrule = $installer->getTable('salesrule/rule');
$tableSalesruleCoupon = $installer->getTable('salesrule/coupon');

$connection = $installer->getConnection();
/** @var $connection Varien_Db_Adapter_Pdo_Mysql */

$connection->addColumn(
    $tableSalesrule,
    'coupon_type',
    'smallint unsigned NOT NULL DEFAULT "' . Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON . '"'
);

/**
 * UPDATE coupon_type to specific in rules with primary coupon
 * Always come to ON DUPLICATE KEY UPDATE section of INSERT statement
 */
$installer->run("
INSERT `{$tableSalesrule}`(
  rule_id, /* PRIMARY KEY */
  /* columns with no default value to prevent warnings */
  description, conditions_serialized, actions_serialized, discount_step
)
SELECT DISTINCT
  rule_id, /* make sure PRIMARY KEY is DUPLICATED */
  '', '', '', 0
FROM `{$tableSalesruleCoupon}`
WHERE
  is_primary IS NOT NULL /* is_primary = 1 */
ON DUPLICATE KEY UPDATE
  coupon_type = '" . Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC . "';
");
