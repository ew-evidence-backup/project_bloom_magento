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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Entity_Setup */

$installer->addAttribute('quote', 'customer_prefix', array('type'=>'static'));
$installer->addAttribute('quote', 'customer_middlename', array('type'=>'static'));
$installer->addAttribute('quote', 'customer_suffix', array('type'=>'static'));

$installer->addAttribute('quote_address', 'prefix', array('type'=>'static'));
$installer->addAttribute('quote_address', 'middlename', array('type'=>'static'));
$installer->addAttribute('quote_address', 'suffix', array('type'=>'static'));
