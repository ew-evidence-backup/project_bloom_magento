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
 * @package     Mage_Persistent
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Remember Me block
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Persistent_Block_Form_Remember extends Mage_Core_Block_Template
{
    /**
     * Prevent rendering if Persistent disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $helper Mage_Persistent_Helper_Data */
        $helper = Mage::helper('persistent');
        return ($helper->isEnabled() && $helper->isRememberMeEnabled()) ? parent::_toHtml() : '';
    }

    /**
     * Is "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        /** @var $helper Mage_Persistent_Helper_Data */
        $helper = Mage::helper('persistent');
        return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
    }
}
