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
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/tab/newsletter.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_newsletter');
        $customer = Mage::registry('current_customer');
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        Mage::register('subscriber', $subscriber);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('customer')->__('Newsletter Information')));

        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => Mage::helper('customer')->__('Subscribed to Newsletter?'),
                    'name'  => 'subscription'
             )
        );

        if ($customer->isReadonly()) {
            $form->getElement('subscription')->setReadonly(true, true);
        }

        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        if($changedDate = $this->getStatusChangedDate()) {
             $fieldset->addField('change_status_date', 'label',
                 array(
                        'label' => $subscriber->isSubscribed() ? Mage::helper('customer')->__('Last Date Subscribed') : Mage::helper('customer')->__('Last Date Unsubscribed'),
                        'value' => $changedDate,
                        'bold'  => true
                 )
            );
        }


        $this->setForm($form);
        return $this;
    }

    public function getStatusChangedDate()
    {
        $subscriber = Mage::registry('subscriber');
        if($subscriber->getChangeStatusAt()) {
            return $this->formatDate($subscriber->getChangeStatusAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        }

        return null;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('adminhtml/customer_edit_tab_newsletter_grid','newsletter.grid')
        );
        return parent::_prepareLayout();
    }

}
