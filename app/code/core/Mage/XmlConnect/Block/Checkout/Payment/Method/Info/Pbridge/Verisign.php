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
 * Payflow Pro (PBridge) Payment info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Pbridge_Verisign extends Mage_Payment_Block_Info_Cc
{
    /**
     * Add CC Save Payment info to order XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        $orderItemXmlObj->addAttribute('type', $this->getMethod()->getCode());
        $orderItemXmlObj->addAttribute(
            'title', $orderItemXmlObj->xmlAttribute($this->getMethod()->getTitle())
        );

        if ($_specificInfo = $this->getSpecificInformation()) {
            foreach ($_specificInfo as $label => $value) {
                $orderItemXmlObj->addCustomChild('item', implode($this->getValueAsArray($value, true), '\n'), array(
                    'label' => $label
                ));
            }
        }
    }
}
