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
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* $installer Mage_Core_Model_Resource_Setup */

$installer->addAttribute('quote',              'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_address',      'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_item',         'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false));
$installer->addAttribute('quote_address_item', 'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false));
$installer->addAttribute('order',              'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item',         'gift_message_id', array('type' => 'int', 'visible' => false, 'required' => false));
$installer->addAttribute('order_item',         'gift_message_available', array('type' => 'int', 'visible' => false, 'required' => false));
