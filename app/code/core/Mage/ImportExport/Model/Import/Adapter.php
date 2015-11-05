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
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Import adapter model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Adapter
{
    /**
     * Adapter factory. Checks for availability, loads and create instance of import adapter object.
     *
     * @param string $type Adapter type ('csv', 'xml' etc.)
     * @param mixed $options OPTIONAL Adapter constructor options
     * @throws Exception
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    public static function factory($type, $options = null)
    {
        if (!is_string($type) || !$type) {
            Mage::throwException(Mage::helper('importexport')->__('Adapter type must be a non empty string'));
        }
        $adapterClass = __CLASS__ . '_' . ucfirst(strtolower($type));

        if (!class_exists($adapterClass)) {
            Mage::throwException("'{$type}' file extension is not supported");
        }
        $adapter = new $adapterClass($options);

        if (! $adapter instanceof Mage_ImportExport_Model_Import_Adapter_Abstract) {
            Mage::throwException(
                Mage::helper('importexport')->__('Adapter must be an instance of Mage_ImportExport_Model_Import_Adapter_Abstract')
            );
        }
        return $adapter;
    }

    /**
     * Create adapter instance for specified source file.
     *
     * @param string $source Source file path.
     * @return Mage_ImportExport_Model_Import_Adapter_Abstract
     */
    public static function findAdapterFor($source)
    {
        return self::factory(pathinfo($source, PATHINFO_EXTENSION), $source);
    }
}
