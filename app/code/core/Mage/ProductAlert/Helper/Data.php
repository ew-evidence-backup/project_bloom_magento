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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * ProductAlert data helper
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Helper_Data extends Mage_Core_Helper_Url
{
    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getCustomer()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getStore()
    {
        return Mage::app()->getStore();
    }

    public function getSaveUrl($type)
    {
        return $this->_getUrl('productalert/add/' . $type, array(
            'product_id'    => $this->getProduct()->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        ));
    }

    public function createBlock($block)
    {
        $error = Mage::helper('core')->__('Invalid block type: %s', $block);
        if (is_string($block)) {
            if (strpos($block, '/') !== false) {
                if (!$block = Mage::getConfig()->getBlockClassName($block)) {
                    Mage::throwException($error);
                }
            }
            $fileName = mageFindClassFile($block);
            if ($fileName!==false) {
                include_once ($fileName);
                $block = new $block(array());
            }
        }
        if (!$block instanceof Mage_Core_Block_Abstract) {
            Mage::throwException($error);
        }
        return $block;
    }
}
