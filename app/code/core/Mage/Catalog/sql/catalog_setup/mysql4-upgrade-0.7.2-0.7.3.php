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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

$this->run("

update {$this->getTable('eav_entity_attribute')} set `sort_order`=10 where `attribute_id`=(select `attribute_id` from {$this->getTable('eav_attribute')} where `attribute_code`='tier_price');

alter table {$this->getTable('catalog_product_entity_tier_price')} add column `all_groups` tinyint (1)UNSIGNED  DEFAULT '1' NOT NULL  after `entity_id`;

");
