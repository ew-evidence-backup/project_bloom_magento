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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;

$installer->startSetup();
$queueTable = $installer->getTable('xmlconnect/queue');
$templateTable = $installer->getTable('xmlconnect/template');

$installer->run("CREATE TABLE `{$queueTable}` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`exec_time` TIMESTAMP NOT NULL ,
`template_id` INT NOT NULL ,
`push_title` VARCHAR( 140 ) NOT NULL ,
`message_title` VARCHAR( 255 ) NOT NULL ,
`content` TEXT NOT NULL ,
`status` TINYINT NOT NULL DEFAULT '0',
`type` VARCHAR( 12 ) NOT NULL ,
`app_code` VARCHAR( 12 ) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin");


$installer->run("CREATE TABLE `{$templateTable}` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`app_type` VARCHAR( 32 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`push_title` VARCHAR( 141 ) NOT NULL ,
`message_title` VARCHAR( 255 ) NOT NULL ,
`content` TEXT NOT NULL ,
`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`modified_at` TIMESTAMP NOT NULL
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_bin");

$installer->getConnection()->addConstraint('FK_TEMPLATE_ID', $queueTable, 'template_id', $templateTable, 'id');
$installer->endSetup();
