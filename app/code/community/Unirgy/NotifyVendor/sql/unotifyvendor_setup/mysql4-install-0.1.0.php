<?php
/**
 * Unirgy_NotifyVendor extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_NotifyVendor
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_NotifyVendor
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */

$this->startSetup();

$cEav = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('catalog_setup');

$cEav->addAttribute('catalog_product', 'unotify_vendor', array(
    'type' => 'int',
    'input' => 'select',
    'label' => 'Notify Vendor',
    'global' => 0,
    'user_defined' => 1,
    'required' => 0,
    'visible' => 1,
));

$this->endSetup();