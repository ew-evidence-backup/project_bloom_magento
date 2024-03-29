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
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Enterprise_GiftCardAccount_Model_Mysql4_Setup */

$tableGCA     = $installer->getTable('enterprise_giftcardaccount/giftcardaccount');
$tableWebsite = $installer->getTable('core/website');

// drop orphan GCAs, modify website_id field to make it compatible with foreign key
$installer->run("DELETE FROM {$tableGCA} WHERE website_id NOT IN (SELECT website_id FROM {$tableWebsite})");
$installer->getConnection()->changeColumn($tableGCA, 'website_id', 'website_id',
    'smallint(5) UNSIGNED NOT NULL DEFAULT 0'
);
$installer->getConnection()->addConstraint('FK_GIFTCARDACCOUNT_WEBSITE_ID', $tableGCA, 'website_id',
    $tableWebsite, 'website_id', 'CASCADE', 'CASCADE'
);
