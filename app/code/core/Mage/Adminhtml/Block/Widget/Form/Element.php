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
 * Form element widget block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Form_Element extends Mage_Adminhtml_Block_Template
{
    protected $_element;
    protected $_form;
    protected $_formBlock;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/form/element.phtml');
    }

    public function setElement($element)
    {
        $this->_element = $element;
        return $this;
    }

    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    public function setFormBlock($formBlock)
    {
        $this->_formBlock = $formBlock;
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->assign('form', $this->_form);
        $this->assign('element', $this->_element);
        $this->assign('formBlock', $this->_formBlock);

        return parent::_beforeToHtml();
    }
}
