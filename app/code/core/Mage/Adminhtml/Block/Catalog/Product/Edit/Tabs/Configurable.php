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
 * admin edit tabs for configurable products
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
//        $product = $this->getProduct();

//        if (!($superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds())) {
            $this->addTab('super_settings', array(
                'label'     => Mage::helper('catalog')->__('Configurable Product Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_settings')->toHtml(),
                'active'    => true
            ));

//        } else {
//            parent::_prepareLayout();
//
//            $this->addTab('configurable', array(
//                'label'     => Mage::helper('catalog')->__('Associated Products'),
//                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config', 'admin.super.config.product')
//                    ->setProductId($this->getRequest()->getParam('id'))
//                    ->toHtml(),
//            ));
//            $this->bindShadowTabs('configurable', 'customer_options');
//        }
    }
}
