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
 * Customer address form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Address_Form extends Mage_Customer_Block_Address_Edit
{
    /**
     * Customer name widget block
     *
     * @var Mage_Customer_Block_Widget_Name
     */
    protected $_nameWidgetBlock;

    /**
     * Enterprise customer field renderer list as type => renderer block
     *
     * Original block relations:
     * - 'text'      => 'enterprise_customer/form_renderer_text',
     * - 'textarea'  => 'enterprise_customer/form_renderer_textarea',
     * - 'multiline' => 'enterprise_customer/form_renderer_multiline',
     * - 'date'      => 'enterprise_customer/form_renderer_date',
     * - 'select'    => 'enterprise_customer/form_renderer_select',
     * - 'multiselect' => 'enterprise_customer/form_renderer_multiselect',
     * - 'boolean'   => 'enterprise_customer/form_renderer_boolean',
     * - 'file'      => 'enterprise_customer/form_renderer_file'
     * - 'image'     => 'enterprise_customer/form_renderer_image'
     *
     * @see customer.xml layout customer_form_template_handle node
     * @var array
     */
    protected $_customerFiledRenderer = array(
        'text'      => 'xmlconnect/customer_form_renderer_text',
        'textarea'  => 'xmlconnect/customer_form_renderer_textarea',
        'multiline' => 'xmlconnect/customer_form_renderer_multiline',
        'date'      => 'xmlconnect/customer_form_renderer_date',
        'select'    => 'xmlconnect/customer_form_renderer_select',
        'multiselect' => 'xmlconnect/customer_form_renderer_multiselect',
        'boolean'   => 'xmlconnect/customer_form_renderer_boolean',
        'file'      => 'xmlconnect/customer_form_renderer_file',
        'image'     => 'xmlconnect/customer_form_renderer_image'
    );

    /**
     * Render customer address form xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $address  = $this->getAddress();

        /**
         * Init address object and save its data to variables
         */
        $addressId = (int)$this->getRequest()->getParam('id');
        $billingChecked = $shippingChecked = false;

        if ($addressId && $address && $address->getId()) {
            $defaultBillingAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
            $defaultShippingAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();

            $billingChecked = (int)$addressId == $defaultBillingAddressId;
            $shippingChecked = (int)$addressId == $defaultShippingAddressId;

            $company    = $address->getCompany();
            $street1    = $address->getStreet(1);
            $street2    = $address->getStreet(2);
            $city       = $address->getCity();
            $regionId   = $address->getRegionId();
            $region     = $address->getRegion();
            $postcode   = $address->getPostcode();
            $countryId  = $address->getCountryId();
            $telephone  = $address->getTelephone();
            $fax        = $address->getFax();
        }

        $action = Mage::helper('xmlconnect')->getActionUrl('xmlconnect/customer/saveaddress');

        /** @var Mage_XmlConnect_Model_Simplexml_Form $fromXmlObj */
        $fromXmlObj = Mage::getModel('xmlconnect/simplexml_form', array(
            'xml_id' => 'address_form',
            'action' => $action,
            'use_container' => true
        ));

        $contactInfoFieldset = $fromXmlObj->addFieldset('contact_info', array(
            'legend' => $this->__('Contact Information')
        ))->setCustomAttributes(array('legend'));

        $this->_addCustomerContactInfo($contactInfoFieldset);

        $contactInfoFieldset->addField('company', 'text', array(
            'label' => $this->__('Company'),
            'value' => isset($company) ? $company : ''
        ));
        $contactInfoFieldset->addField('telephone', 'text', array(
            'label' => $this->__('Telephone'),
            'required' => 'true',
            'value' => isset($telephone) ? $telephone : ''
        ));
        $contactInfoFieldset->addField('fax', 'text', array(
            'label' => $this->__('Fax'),
            'value' => isset($fax) ? $fax : ''
        ));

        $addressFieldset = $fromXmlObj->addFieldset('address_info', array('legend' => $this->__('Address')))
            ->setCustomAttributes(array('legend'));

        $addressFieldset->addField('street', 'text', array(
            'name' => 'street[]',
            'label' => $this->__('Street Address'),
            'required' => 'true',
            'value' => isset($street1) ? $street1 : ''
        ));
        $addressFieldset->addField('street_2', 'text', array(
            'name' => 'street[]',
            'label' => $this->__('Street Address 2'),
            'value' => isset($street2) ? $street2 : ''
        ));
        $addressFieldset->addField('city', 'text', array(
            'label' => $this->__('City'),
            'required' => 'true',
            'value' => isset($city) ? $city : ''
        ));

        $countryId  = isset($countryId) ? $countryId    : null;
        $regionId   = isset($regionId)  ? $regionId     : null;
        $region     = isset($region)    ? $region       : null;

        $addressFieldset->addField('country_id', 'countryListSelect', array(
            'label'     => $this->__('Country'),
            'required'  => 'true',
            'value'     => array(
                'country_id'    => $countryId,
                'region_id'     => $regionId,
                'region'        => $region
            ),
            'old_format' => true
        ));
        $addressFieldset->addField('region', 'text', array(
            'label' => $this->__('State/Province'),
            'value' => isset($region) ? $region : ''
        ));
        $addressFieldset->addField('region_id', 'select', array(
            'label' => $this->__('State/Province'),
            'required' => 'true',
        ));
        $addressFieldset->addField('postcode', 'text', array(
            'label' => $this->__('Zip/Postal Code'),
            'required' => 'true',
            'value' => isset($postcode) ? $postcode : ''
        ));
        $addressFieldset->addField('default_billing', 'checkbox', array(
            'label' => $this->__('Use as my default billing address'),
            'value' => $billingChecked ? $billingChecked : 0
        ));

        $addressFieldset->addField('default_shipping', 'checkbox', array(
            'label' => $this->__('Use as my default shipping address'),
            'value' => $shippingChecked ? $shippingChecked : 0
        ));

        $this->_addCustomAddressAttributes($addressFieldset);

        return $fromXmlObj->getXml();
    }

    /**
     * Add customer contact attributes
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addCustomerContactInfo(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
    ) {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Customer'))) {
            $this->setNameWidgetBlock(
                $this->getLayout()->createBlock('customer/widget_name')->setObject(
                    $this->getAddress()->getFirstname() ? $this->getAddress() : $this->getCustomer()
            ));

            if ($this->getNameWidgetBlock()->showPrefix()) {
                $this->_addPrefix($contactInfoFieldset);
            }

            $this->_addFirstName($contactInfoFieldset);

            if ($this->getNameWidgetBlock()->showMiddlename()) {
                $this->_addMiddleName($contactInfoFieldset);
            }

            $this->_addLastName($contactInfoFieldset);

            if ($this->getNameWidgetBlock()->showSuffix()) {
                $this->_addSuffix($contactInfoFieldset);
            }
        } else {
            $this->_addFirstName($contactInfoFieldset);
            $this->_addLastName($contactInfoFieldset);
        }
        return $this;
    }

    /**
     * Add customer prefix field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addPrefix(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset)
    {
        $attributes = array();
        $attributes += $contactInfoFieldset->checkAttribute(
            'value', $this->getNameWidgetBlock()->getObject()->getPrefix()
        );

        $attributes += $contactInfoFieldset->checkAttribute(
            'required', (int)$this->getNameWidgetBlock()->isPrefixRequired()
        );

        if ($this->getNameWidgetBlock()->getPrefixOptions() === false) {
            $contactInfoFieldset->addField($this->getNameWidgetBlock()->getFieldId('prefix'), 'text', array(
                'label' => $this->getNameWidgetBlock()->__('Prefix'),
                'name' => $this->getNameWidgetBlock()->getFieldName('prefix')
            ) + $attributes);
        } else {
            $contactInfoFieldset->addField($this->getNameWidgetBlock()->getFieldId('prefix'), 'select', array(
                'label' => $this->getNameWidgetBlock()->__('Prefix'),
                'name' => $this->getNameWidgetBlock()->getFieldName('prefix'),
                'options' => $this->getNameWidgetBlock()->getPrefixOptions()
            ) + $attributes);
        }
        return $this;
    }

    /**
     * Add customer suffix field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addSuffix(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
    )
    {
        $attributes = array();
        $attributes += $contactInfoFieldset->checkAttribute(
            'value', $this->getNameWidgetBlock()->getObject()->getSuffix()
        );

        $attributes += $contactInfoFieldset->checkAttribute(
            'required', (int)$this->getNameWidgetBlock()->isSuffixRequired()
        );

        if ($this->getNameWidgetBlock()->getSuffixOptions() === false) {
            $contactInfoFieldset->addField($this->getNameWidgetBlock()->getFieldId('suffix'), 'text', array(
                'label' => $this->getNameWidgetBlock()->__('Suffix'),
                'name' => $this->getNameWidgetBlock()->getFieldName('suffix')
            ) + $attributes);
        } else {
            $contactInfoFieldset->addField($this->getNameWidgetBlock()->getFieldId('suffix'), 'select', array(
                'label' => $this->getNameWidgetBlock()->__('Suffix'),
                'name' => $this->getNameWidgetBlock()->getFieldName('suffix'),
                'options' => $this->getNameWidgetBlock()->getSuffixOptions()
            ) + $attributes);
        }
        return $this;
    }

    /**
     * Add customer middle name field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addMiddleName(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
    )
    {
        $attributes = array();
        $attributes += $contactInfoFieldset->checkAttribute(
            'value',
            $this->getNameWidgetBlock()->getObject()->getMiddlename()
        );

        $contactInfoFieldset->addField($this->getNameWidgetBlock()->getFieldId('middlename'), 'text', array(
            'label' => $this->getNameWidgetBlock()->__('M.I.'),
            'name' => $this->getNameWidgetBlock()->getFieldName('middlename')
        ) + $attributes);

        return $this;
    }

    /**
     * Add customer first name field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addFirstName(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
    ) {
        $firstName  = $this->getAddress()->getFirstname();
        $contactInfoFieldset->addField('firstname', 'text', array(
            'label' => $this->__('First Name'),
            'required' => 'true',
            'value' => isset($firstName) ? $firstName : ''
        ));
        return $this;
    }

    /**
     * Add customer last name field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addLastName(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $contactInfoFieldset
    ) {
        $lastName   = $this->getAddress()->getLastname();
        $contactInfoFieldset->addField('lastname', 'text', array(
            'label' => $this->__('Last Name'),
            'required' => 'true',
            'value' => isset($lastName) ? $lastName : ''
        ));
        return $this;
    }

    /**
     * Add custom customer attributes
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $addressFieldset
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    protected function _addCustomAddressAttributes(
        Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $addressFieldset
    ) {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Customer'))) {
            /** @var $addressAttrBlock Enterprise_Customer_Block_Form */
            $addressAttrBlock = $this->getLayout()
                ->addBlock('enterprise_customer/form', 'customer_address_attr');
            $addressAttrBlock->setFormCode('customer_address_edit');
            $addressAttrBlock->setEntity($this->getAddress());

            foreach ($this->_customerFiledRenderer as $type => $rendererBlock) {
                $addressAttrBlock->addRenderer($type, $rendererBlock, 'customer/form/renderer/text.phtml');
            }

            if ($addressAttrBlock->hasUserDefinedAttributes()) {
                foreach ($addressAttrBlock->getUserDefinedAttributes() as $attribute) {
                    $type   = $attribute->getFrontendInput();
                    $block  = $addressAttrBlock->getRenderer($type);
                    if ($block) {
                        $block->setAttributeObject($attribute)->setEntity($addressAttrBlock->getEntity())
                            ->addFieldToXmlObj($addressFieldset);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Get customer name widget block
     *
     * @return Mage_Customer_Block_Widget_Name
     */
    public function getNameWidgetBlock()
    {
        return $this->_nameWidgetBlock;
    }

    /**
     * Set customer name widget block
     *
     * @param Mage_Customer_Block_Widget_Name $nameWidgetBlock
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    public function setNameWidgetBlock($nameWidgetBlock)
    {
        $this->_nameWidgetBlock = $nameWidgetBlock;
        return $this;
    }

    /**
     * Get enterprise customer fields renderer
     *
     * @return array
     */
    public function getCustomerFiledRenderer()
    {
        return $this->_customerFiledRenderer;
    }

    /**
     * Set enterprise customer fields renderer
     *
     * @param array $customerFiledRenderer
     * @return Mage_XmlConnect_Block_Customer_Address_Form
     */
    public function setCustomerFiledRenderer($customerFiledRenderer)
    {
        $this->_customerFiledRenderer = $customerFiledRenderer;
        return $this;
    }
}
