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

/* @var $installer Mage_Paypal_Model_Mysql4_Setup */
$installer = $this;

$installer->run("
CREATE TABLE `{$installer->getTable('paypal/cert')}` (
    `cert_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    `content` MEDIUMBLOB NOT NULL,
    `updated_at` datetime default NULL,
    PRIMARY KEY (`cert_id`),
    KEY `IDX_PAYPAL_CERT_WEBSITE` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_PAYPAL_CERT_WEBSITE',
    $this->getTable('paypal/cert'),
    'website_id',
    $this->getTable('core/website'),
    'website_id'
);
