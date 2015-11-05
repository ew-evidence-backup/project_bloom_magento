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
 * Adminhtml block for result of catalog product composite update
 * Forms response for a popup window for a case when form is directly submitted
 * for single item
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Update_Result extends Mage_Core_Block_Template
{
    /**
     * Forms script response
     *
     * @return string
     */
    public function _toHtml()
    {
        $updateResult = Mage::registry('composite_update_result');
        $resultJson = Mage::helper('core')->jsonEncode($updateResult);
        $jsVarname = $updateResult->getJsVarName();
        return Mage::helper('adminhtml/js')->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
    }
}
