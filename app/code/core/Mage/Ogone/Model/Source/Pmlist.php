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
 * @package     Mage_Ogone
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Ogone template Action Dropdown source
 */
class Mage_Ogone_Model_Source_Pmlist
{
    /**
     * Prepare ogone payment block layout as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Ogone_Model_Api::PMLIST_HORISONTAL_LEFT, 'label' => Mage::helper('ogone')->__('Horizontally grouped logo with group name on left')),
            array('value' => Mage_Ogone_Model_Api::PMLIST_HORISONTAL, 'label' => Mage::helper('ogone')->__('Horizontally grouped logo with no group name')),
            array('value' => Mage_Ogone_Model_Api::PMLIST_VERTICAL, 'label' => Mage::helper('ogone')->__('Verical list')),
        );
    }
}
