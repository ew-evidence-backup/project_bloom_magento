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
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'subtotal_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'shipping_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_subtotal_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_tax_invoiced', 'decimal(12,4) NULL');
$installer->getConnection()->addColumn($this->getTable('sales_order'), 'base_shipping_invoiced', 'decimal(12,4) NULL');

$installer->addAttribute('order', 'subtotal_invoiced', array('type'=>'static'));
$installer->addAttribute('order', 'tax_invoiced', array('type'=>'static'));
$installer->addAttribute('order', 'shipping_invoiced', array('type'=>'static'));
$installer->addAttribute('order', 'base_subtotal_invoiced', array('type'=>'static'));
$installer->addAttribute('order', 'base_tax_invoiced', array('type'=>'static'));
$installer->addAttribute('order', 'base_shipping_invoiced', array('type'=>'static'));
