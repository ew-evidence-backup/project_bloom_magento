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
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Add foreign key to customers table into wishlists
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$tableWishlist  = $this->getTable('wishlist');
$tableCustomers = $this->getTable('customer/entity');

$installer->run("DELETE FROM {$tableWishlist} WHERE customer_id NOT IN (SELECT entity_id FROM {$tableCustomers})");

$installer->run("
ALTER TABLE {$tableWishlist}
    ADD CONSTRAINT `FK_CUSTOMER` FOREIGN KEY (`customer_id`)
    REFERENCES {$tableCustomers} (`entity_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");

$installer->endSetup();
