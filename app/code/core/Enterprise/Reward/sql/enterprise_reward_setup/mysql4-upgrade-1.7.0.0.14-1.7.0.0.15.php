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


/* @var $installer Enterprise_Reward_Model_Mysql4_Setup */
$installer = $this;

$attributes = array(
    'reward_update_notification',
    'reward_warning_notification'
);
$entityTypeCode = 'customer';

foreach ($attributes as $attributeCode) {
    $attributeId = $installer->getAttributeId($entityTypeCode, $attributeCode);
    $installer->updateAttribute($entityTypeCode, $attributeId, 'is_user_defined', 0);
    $installer->updateAttribute($entityTypeCode, $attributeId, 'is_system', 1);
    $installer->updateAttribute($entityTypeCode, $attributeId, 'is_hidden', 1);
}
