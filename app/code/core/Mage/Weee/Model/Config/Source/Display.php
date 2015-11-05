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
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */
class Mage_Weee_Model_Config_Source_Display
{

    public function toOptionArray()
    {
        /**
         * VAT is not applicable to FPT separately (we can't have FPT incl/excl VAT)
         */
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('weee')->__('Including FPT only')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('weee')->__('Including FPT and FPT description')
            ),
            //array('value'=>4, 'label'=>Mage::helper('weee')->__('Including FPT and FPT description [incl. FPT VAT]')),
            array(
                'value' => 2,
                'label' => Mage::helper('weee')->__('Excluding FPT, FPT description, final price')
            ),
            array(
                'value' => 3,
                'label' => Mage::helper('weee')->__('Excluding FPT')
            ),
        );
    }

}
