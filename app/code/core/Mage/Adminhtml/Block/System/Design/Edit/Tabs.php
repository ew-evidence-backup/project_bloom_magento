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

class Mage_Adminhtml_Block_System_Design_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('design_tabs');
        $this->setDestElementId('design_edit_form');
        $this->setTitle(Mage::helper('core')->__('Design Change'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('core')->__('General'),
            'content'   => $this->getLayout()->createBlock('adminhtml/system_design_edit_tab_general')->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}
