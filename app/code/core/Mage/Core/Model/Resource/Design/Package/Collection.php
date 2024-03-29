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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Design package collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Design_Package_Collection extends Varien_Object
{
    /**
     * Load design package collection
     *
     * @return Mage_Core_Model_Resource_Design_Package_Collection
     */
    public function load()
    {
        $packages = $this->getData('packages');
        if (is_null($packages)) {
            $packages = Mage::getModel('core/design_package')->getPackageList();
            $this->setData('packages', $packages);
        }

        return $this;
    }

    /**
     * Convert to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $packages = $this->getData('packages');
        foreach ($packages as $package) {
            $options[] = array('value' => $package, 'label' => $package);
        }
        array_unshift($options, array('value' => '', 'label' => ''));

        return $options;
    }
}
