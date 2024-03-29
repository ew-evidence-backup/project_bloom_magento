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
 * Manage currency import services block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Currency_Rate_Services extends Mage_Adminhtml_Block_Template
{

    /**
     * Set import services template
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('system/currency/rate/services.phtml');
    }

    /**
     * Create import services form select element
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('import_services',
            $this->getLayout()->createBlock('adminhtml/html_select')
            ->setOptions(Mage::getModel('adminhtml/system_config_source_currency_service')->toOptionArray(0))
            ->setId('rate_services')
            ->setName('rate_services')
            ->setValue(Mage::getSingleton('adminhtml/session')->getCurrencyRateService(true))
            ->setTitle(Mage::helper('adminhtml')->__('Import Service'))
        );

        return parent::_prepareLayout();
    }

}
