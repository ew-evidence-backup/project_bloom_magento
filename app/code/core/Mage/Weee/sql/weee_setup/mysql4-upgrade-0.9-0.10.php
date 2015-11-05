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
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$installer = $this;
/* @var $installer Mage_Weee_Model_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('quote_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('quote_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('quote_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));


$installer->addAttribute('order_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('order_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('order_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));


$installer->addAttribute('creditmemo_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('creditmemo_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('creditmemo_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));


$installer->addAttribute('invoice_item', 'weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'weee_tax_row_disposition', array('type'=>'decimal'));

$installer->addAttribute('invoice_item', 'base_weee_tax_disposition', array('type'=>'decimal'));
$installer->addAttribute('invoice_item', 'base_weee_tax_row_disposition', array('type'=>'decimal'));

$installer->endSetup();
