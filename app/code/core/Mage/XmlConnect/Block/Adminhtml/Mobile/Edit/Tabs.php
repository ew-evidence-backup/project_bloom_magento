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
 * Application Tabs block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor
     * Setting grid_id, DOM destination element id, Title
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('mobile_app_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Manage Mobile App'));
    }

    /**
     * Preparing global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (Mage::getSingleton('adminhtml/session')->getNewApplication()) {
            $this->addTab('set', array(
                'label'     => $this->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('xmlconnect/adminhtml_mobile_edit_tab_settings')
                    ->toHtml(),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }
}
