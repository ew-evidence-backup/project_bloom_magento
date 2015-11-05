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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('tag/tag'), 'first_store_id', "smallint(5) UNSIGNED NOT NULL DEFAULT '0'");

$groupedTags = $installer->getConnection()->select()
    ->from($installer->getTable('tag/relation'))->group('tag_id')->order('created_at ASC');
$select = $installer->getConnection()->select()
    ->reset()
    ->joinInner(array('relation_table' => new Zend_Db_Expr("({$groupedTags->__toString()})")),
        'relation_table.tag_id = main_table.tag_id', null)
    ->columns(array('first_store_id' => 'store_id'));

$updateSql = $select->crossUpdateFromSelect(array('main_table' => $installer->getTable('tag/tag')));
$installer->getConnection()->query($updateSql);

$installer->endSetup();
