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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

//Entries for 'Newfoundland' should be corrected because of the province changed its name and code

$installer->run("
    UPDATE {$installer->getTable('directory/country_region')}
    SET code = 'NL', default_name = 'Newfoundland and Labrador'
    WHERE region_id = 69
");

$installer->run("
    UPDATE {$installer->getTable('directory/country_region_name')}
    SET `name` = 'Newfoundland and Labrador'
    WHERE `region_id` = 69 AND `name` = 'Newfoundland'
");

$installer->endSetup();
