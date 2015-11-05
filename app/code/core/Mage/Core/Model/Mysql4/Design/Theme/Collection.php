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

class Mage_Core_Model_Mysql4_Design_Theme extends Varien_Directory_Collection
{
    public function load()
    {
        $packages = $this->getData('themes');
        if (is_null($packages)) {
            $packages = Mage::getModel('core/design_package')->getThemeList();
            $this->setData('themes', $packages);
        }

        return $this;
    }

    public function toOptionArray()
    {
        $options = array();
        $packages = $this->getData('themes');
        foreach ($packages as $package) {
            $options[] = array('value'=>$package, 'label'=>$package);
        }
        array_unshift($options, array('value'=>'', 'label'=>''));

        return $options;
    }
}
