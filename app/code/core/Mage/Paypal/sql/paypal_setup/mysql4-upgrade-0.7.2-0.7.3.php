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
$installer = $this;
/* @var $installer Mage_Paypal_Model_Mysql4_Setup */
$installer->startSetup();

$installer->addAttribute('order_payment', 'cc_secure_verify', array());

// move paypal style settings to new paths
foreach (array(
        'paypal/wpp/page_style' => 'paypal/style/page_style',
        'paypal/wps/logo_url' => 'paypal/style/logo_url',
    ) as $from => $to) {
    $installer->run("
    UPDATE {$installer->getTable('core/config_data')} SET `path` = '{$to}'
    WHERE `path` = '{$from}'
    ");
}
$installer->endSetup();
