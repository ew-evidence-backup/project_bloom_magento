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
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Tax Rate SQL upgrade
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
    ALTER TABLE {$this->getTable('tax_rate_data')} DROP FOREIGN KEY `FK_TAX_RATE_DATA_TAX_RATE`;
    ALTER TABLE {$this->getTable('tax_rate')} CHANGE `tax_rate_id` `tax_rate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
    ALTER TABLE {$this->getTable('tax_rate_data')} CHANGE `tax_rate_data_id` `tax_rate_data_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
    ALTER TABLE {$this->getTable('tax_rate_data')} CHANGE `tax_rate_id` `tax_rate_id` INT UNSIGNED NOT NULL DEFAULT '0';
    ALTER TABLE {$this->getTable('tax_rate_data')} ADD CONSTRAINT `FK_TAX_RATE_DATA_TAX_RATE` FOREIGN KEY (`tax_rate_id`) REFERENCES {$this->getTable('tax_rate')} (`tax_rate_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");
$installer->endSetup();
