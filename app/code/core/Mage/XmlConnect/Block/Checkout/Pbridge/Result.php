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
 * Pbridge result payment block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Pbridge_Result extends Mage_Core_Block_Template
{
    /**
     * Return url for redirect with params of Payment Bridge incoming data
     *
     * @return string
     */
    public function getPbridgeParamsAsUrl()
    {
        $pbParams = Mage::helper('enterprise_pbridge')->getPbridgeParams();
        $params = array_merge(
            array('_nosid' => true, 'method' => 'pbridge_' . $pbParams['original_payment_method']),
            $pbParams
        );
        return Mage::getUrl('xmlconnect/pbridge/output', $params);
    }
}
