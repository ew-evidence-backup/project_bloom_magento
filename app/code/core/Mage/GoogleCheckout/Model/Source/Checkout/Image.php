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
 * @package     Mage_GoogleCheckout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


class Mage_GoogleCheckout_Model_Source_Checkout_Image
{
    public function toOptionArray()
    {
        $sizes = array(
            '180/46' => Mage::helper('googlecheckout')->__('Large - %s', '180x46'),
            '168/44' => Mage::helper('googlecheckout')->__('Medium - %s', '168x44'),
            '160/43' => Mage::helper('googlecheckout')->__('Small - %s', '160x43'),
        );

        $styles = array(
            'trans' => Mage::helper('googlecheckout')->__('Transparent'),
            'white' => Mage::helper('googlecheckout')->__('White Background'),
        );

        $options = array();
        foreach ($sizes as $size => $sizeLabel) {
            foreach ($styles as $style => $styleLabel) {
                $options[] = array(
                    'value' => $size . '/' . $style,
                    'label' => $sizeLabel . ' (' . $styleLabel . ')'
                );
            }
        }

        return $options;
    }
}
