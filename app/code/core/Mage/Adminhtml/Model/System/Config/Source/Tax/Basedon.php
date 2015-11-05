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
class Mage_Adminhtml_Model_System_Config_Source_Tax_Basedon
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shipping', 'label'=>Mage::helper('adminhtml')->__('Shipping Address')),
            array('value'=>'billing', 'label'=>Mage::helper('adminhtml')->__('Billing Address')),
            array('value'=>'origin', 'label'=>Mage::helper('adminhtml')->__("Shipping Origin")),
        );
    }

}
