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
 * Admin poll answer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Poll_Answer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll_answer';

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $answerData = Mage::getModel('poll/poll_answer')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('answer_data', $answerData);
        }

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/poll/edit', array('id' => $answerData->getPollId(), 'tab' => 'answers_section')) . '\');');
        $this->_updateButton('save', 'label', Mage::helper('poll')->__('Save Answer'));
        $this->_updateButton('delete', 'label', Mage::helper('poll')->__('Delete Answer'));
    }

    public function getHeaderText()
    {
        return Mage::helper('poll')->__("Edit Answer '%s'", $this->htmlEscape(Mage::registry('answer_data')->getAnswerTitle()));
    }

}
