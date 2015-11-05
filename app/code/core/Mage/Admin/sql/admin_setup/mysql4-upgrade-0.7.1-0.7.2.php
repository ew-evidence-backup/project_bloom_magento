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
 * @package     Mage_Admin
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$tableAdmins = $installer->getTable('admin/user');

// delete admin username duplicates
$duplicatedUsers = $installer->getConnection()->fetchPairs("
SELECT user_id, username FROM {$tableAdmins} GROUP by username HAVING COUNT(user_id) > 1
");
$installer->run("DELETE FROM {$tableAdmins} WHERE username "
    . $installer->getConnection()->quoteInto('IN (?) ', array_values($duplicatedUsers))
    . 'AND user_id ' . $installer->getConnection()->quoteInto('NOT IN (?) ', array_keys($duplicatedUsers))
);

// add unique key to username field
$installer->getConnection()->addKey($tableAdmins, 'UNQ_ADMIN_USER_USERNAME', 'username', 'unique');

$installer->endSetup();
