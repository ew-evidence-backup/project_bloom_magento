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
 * Adminhtml store edit
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        switch (Mage::registry('store_type')) {
            case 'website':
                $this->_objectId = 'website_id';
                $saveLabel   = Mage::helper('core')->__('Save Website');
                $deleteLabel = Mage::helper('core')->__('Delete Website');
                $deleteUrl   = $this->getUrl('*/*/deleteWebsite', array('item_id' => Mage::registry('store_data')->getId()));
                break;
            case 'group':
                $this->_objectId = 'group_id';
                $saveLabel   = Mage::helper('core')->__('Save Store');
                $deleteLabel = Mage::helper('core')->__('Delete Store');
                $deleteUrl   = $this->getUrl('*/*/deleteGroup', array('item_id' => Mage::registry('store_data')->getId()));
                break;
            case 'store':
                $this->_objectId = 'store_id';
                $saveLabel   = Mage::helper('core')->__('Save Store View');
                $deleteLabel = Mage::helper('core')->__('Delete Store View');
                $deleteUrl   = $this->getUrl('*/*/deleteStore', array('item_id' => Mage::registry('store_data')->getId()));
                break;
        }
        $this->_controller = 'system_store';

        parent::__construct();

        $this->_updateButton('save', 'label', $saveLabel);
        $this->_updateButton('delete', 'label', $deleteLabel);
        $this->_updateButton('delete', 'onclick', 'setLocation(\''.$deleteUrl.'\');');

        if (!Mage::registry('store_data')->isCanDelete()) {
            $this->_removeButton('delete');
        }
        if (Mage::registry('store_data')->isReadOnly()) {
            $this->_removeButton('save')->_removeButton('reset');
        }
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        switch (Mage::registry('store_type')) {
            case 'website':
                $editLabel = Mage::helper('core')->__('Edit Website');
                $addLabel  = Mage::helper('core')->__('New Website');
                break;
            case 'group':
                $editLabel = Mage::helper('core')->__('Edit Store');
                $addLabel  = Mage::helper('core')->__('New Store');
                break;
            case 'store':
                $editLabel = Mage::helper('core')->__('Edit Store View');
                $addLabel  = Mage::helper('core')->__('New Store View');
                break;
        }

        return Mage::registry('store_action') == 'add' ? $addLabel : $editLabel;
    }
}
