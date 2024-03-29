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
 * Backend ajax controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Ajax action for inline translation
     *
     */
    public function translateAction()
    {
        $translation = $this->getRequest()->getPost('translate');
        $area = $this->getRequest()->getPost('area');

        //filtering
        /** @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getModel('core/input_filter_maliciousCode');
        foreach ($translation as &$item) {
            $item['custom'] = $filter->filter($item['custom']);
        }

        echo Mage::helper('core/translate')->apply($translation, $area);
        exit();
    }
}
