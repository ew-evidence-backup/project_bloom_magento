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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Xmlconnect Config data install
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$appTableName = $installer->getTable('xmlconnect/application');

$configField = $installer
    ->getConnection()
    ->tableColumnExists($appTableName, 'configuration');

if ($configField) {
    /** @var $appModel Mage_XmlConnect_Model_Application */
    $appModel = Mage::getModel('xmlconnect/application');
    $select = $appModel->getResource()
        ->getReadConnection()
        ->select()
        ->from(
            $appTableName,
            array('application_id', 'configuration')
    );

    $result = $appModel
        ->getResource()
        ->getReadConnection()
        ->fetchAll($select);

    foreach ($result as $rows) {
        if (empty($rows['configuration'])) {
            continue;
        }
        $deprecatedConfig = unserialize($rows['configuration']);
        $appModel->getConfigModel()->saveConfig(
            $rows['application_id'],
            $appModel->convertOldConfing($deprecatedConfig),
            Mage_XmlConnect_Model_Application::DEPRECATED_CONFIG_FLAG
        );
    }

    $installer
        ->getConnection()
        ->dropColumn(
            $installer->getTable('xmlconnect/application'),
            'configuration'
    );
}
