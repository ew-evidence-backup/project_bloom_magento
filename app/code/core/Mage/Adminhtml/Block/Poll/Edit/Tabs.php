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
 * Admin poll left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('poll_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('poll')->__('Poll Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('poll')->__('Poll Information'),
            'title'     => Mage::helper('poll')->__('Poll Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/poll_edit_tab_form')->toHtml(),
        ))
        ;

        $this->addTab('answers_section', array(
                'label'     => Mage::helper('poll')->__('Poll Answers'),
                'title'     => Mage::helper('poll')->__('Poll Answers'),
                'content'   => $this->getLayout()->createBlock('adminhtml/poll_edit_tab_answers')
                                ->append($this->getLayout()->createBlock('adminhtml/poll_edit_tab_answers_list'))
                                ->toHtml(),
                'active'    => ( $this->getRequest()->getParam('tab') == 'answers_section' ) ? true : false,
            ));
        return parent::_beforeToHtml();
    }
}
