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
$installer->startSetup();

$installer->run("CREATE TABLE `{$this->getTable('enterprise_giftcardaccount')}` (
                    `giftcardaccount_id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY,
                    `code` varchar(50) NOT NULL,
                    `status` tinyint(1) NOT NULL,
                    `date_created` date NOT NULL,
                    `date_expires` date DEFAULT NULL,
                    `website_id` smallint(5) NOT NULL,
                    `balance` decimal(12,4) NOT NULL default '0.0000'
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();
