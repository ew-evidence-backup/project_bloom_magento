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
 * @package     Mage_Poll
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * FOREIGN KEY update
 *
 * @category   Mage
 * @package    Mage_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('poll_store')}
    DROP FOREIGN KEY `FK_POLL_STORE`;
ALTER TABLE {$this->getTable('poll_store')}
    ADD CONSTRAINT `FK_POLL_STORE_POLL` FOREIGN KEY (`poll_id`)
    REFERENCES {$this->getTable('poll')} (`poll_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");
$installer->run("
ALTER TABLE {$this->getTable('poll')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NULL DEFAULT '0';
ALTER TABLE {$this->getTable('poll')}
    ADD CONSTRAINT `FK_POLL_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
");
$installer->run("
ALTER TABLE {$this->getTable('poll_store')}
    ADD CONSTRAINT `FK_POLL_STORE_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;
");
$installer->endSetup();
