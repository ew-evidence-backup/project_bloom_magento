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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_reward/reward_salesrule')}` (
    `rule_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `points_delta` int(11) UNSIGNED NOT NULL DEFAULT '0',
    KEY `FK_REWARD_SALESRULE_RULE_ID` (`rule_id`),
    CONSTRAINT `FK_REWARD_SALESRULE_RULE_ID` FOREIGN KEY (`rule_id`) REFERENCES `{$installer->getTable('salesrule/rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
");

$installer->addAttribute('order', 'reward_salesrule_points', array('type' => 'int'));

$installer->endSetup();
