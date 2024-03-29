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
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
$websites = $conn->fetchPairs("SELECT store_id, website_id FROM {$this->getTable('core_store')}");

$conn->addColumn($this->getTable('salesrule'), 'website_ids', 'text');

$select = $conn->select()
    ->from($this->getTable('salesrule'), array('rule_id', 'store_ids'));
$rows = $conn->fetchAll($select);

foreach ($rows as $r) {
    $websiteIds = array();
    foreach (explode(',',$r['store_ids']) as $storeId) {
        if ($storeId!=='') {
            $websiteIds[$websites[$storeId]] = true;
        }
    }
    $conn->update($this->getTable('salesrule'),
        array('website_ids'=>join(',',array_keys($websiteIds))),
        "rule_id=".$r['rule_id']
    );
}
$conn->dropColumn($this->getTable('salesrule'), 'store_ids');

$installer->endSetup();
