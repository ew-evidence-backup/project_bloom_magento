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
 * ImportExport config model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Config
{
    /**
     * Get data about models from specified config key.
     *
     * @static
     * @param string $configKey
     * @throws Mage_Core_Exception
     * @return array
     */
    public static function getModels($configKey)
    {
        $entities = array();

        foreach (Mage::getConfig()->getNode($configKey)->asCanonicalArray() as $entityType => $entityParams) {
            if (empty($entityParams['model_token'])) {
                Mage::throwException(Mage::helper('importexport')->__('Node does not has model token tag'));
            }
            $entities[$entityType] = array(
                'model' => $entityParams['model_token'],
                'label' => empty($entityParams['label']) ? $entityType : $entityParams['label']
            );
        }
        return $entities;
    }

    /**
     * Get model params as combo-box options.
     *
     * @static
     * @param string $configKey
     * @param boolean $withEmpty OPTIONAL Include 'Please Select' option or not
     * @return array
     */
    public static function getModelsComboOptions($configKey, $withEmpty = false)
    {
        $options = array();

        if ($withEmpty) {
            $options[] = array('label' => Mage::helper('importexport')->__('-- Please Select --'), 'value' => '');
        }
        foreach (self::getModels($configKey) as $type => $params) {
            $options[] = array('value' => $type, 'label' => $params['label']);
        }
        return $options;
    }

    /**
     * Get model params as array of options.
     *
     * @static
     * @param string $configKey
     * @param boolean $withEmpty OPTIONAL Include 'Please Select' option or not
     * @return array
     */
    public static function getModelsArrayOptions($configKey, $withEmpty = false)
    {
        $options = array();
        if ($withEmpty) {
            $options[0] = Mage::helper('importexport')->__('-- Please Select --');
        }
        foreach (self::getModels($configKey) as $type => $params) {
            $options[$type] = $params['label'];
        }
        return $options;
    }
}
