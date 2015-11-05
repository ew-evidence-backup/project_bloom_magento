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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Product option types mode source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type
{
    const PRODUCT_OPTIONS_GROUPS_PATH = 'global/catalog/product/options/custom/groups';

    public function toOptionArray()
    {
        $groups = array(
            array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please select --'))
        );

        foreach (Mage::getConfig()->getNode(self::PRODUCT_OPTIONS_GROUPS_PATH)->children() as $group) {
            $types = array();
            $typesPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types';
            foreach (Mage::getConfig()->getNode($typesPath)->children() as $type) {
                $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types/' . $type->getName() . '/label';
                $types[] = array(
                    'label' => (string) Mage::getConfig()->getNode($labelPath),
                    'value' => $type->getName()
                );
            }

            $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/label';

            $groups[] = array(
                'label' => (string) Mage::getConfig()->getNode($labelPath),
                'value' => $types
            );
        }

        return $groups;
    }
}
