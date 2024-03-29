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
 * Admin product tax class add form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rate_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_titles = null;

    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('rate_form');
        $this->setTemplate('tax/rate/form.phtml');
    }

    protected function _prepareForm()
    {
        $rateId = (int)$this->getRequest()->getParam('rate');
        $rateObject = new Varien_Object();
        $rateModel  = Mage::getSingleton('tax/calculation_rate');
        $rateObject->setData($rateModel->getData());

        $form = new Varien_Data_Form();

        $countries = Mage::getModel('adminhtml/system_config_source_country')
            ->toOptionArray();
        unset($countries[0]);

        if (!$rateObject->hasTaxCountryId()) {
            $rateObject->setTaxCountryId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY));
        }

        if (!$rateObject->hasTaxRegionId()) {
            $rateObject->setTaxRegionId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION));
        }

        $regionCollection = Mage::getModel('directory/region')
            ->getCollection()
            ->addCountryFilter($rateObject->getTaxCountryId());

        $regions = $regionCollection->toOptionArray();

        if ($regions) {
            $regions[0]['label'] = '*';
        } else {
            $regions = array(array('value'=>'', 'label'=>'*'));
        }

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('tax')->__('Tax Rate Information')));

        if( $rateObject->getTaxCalculationRateId() > 0 ) {
            $fieldset->addField('tax_calculation_rate_id', 'hidden',
                array(
                    'name' => "tax_calculation_rate_id",
                    'value' => $rateObject->getTaxCalculationRateId()
                )
            );
        }

        $fieldset->addField('code', 'text',
            array(
                'name' => 'code',
                'label' => Mage::helper('tax')->__('Tax Identifier'),
                'title' => Mage::helper('tax')->__('Tax Identifier'),
                'class' => 'required-entry',
                'value' => $rateModel->getCode(),
                'required' => true,
            )
        );

        $fieldset->addField('tax_country_id', 'select',
            array(
                'name' => 'tax_country_id',
                'label' => Mage::helper('tax')->__('Country'),
                'required' => true,
                'values' => $countries
            )
        );

        $fieldset->addField('tax_region_id', 'select',
            array(
                'name' => 'tax_region_id',
                'label' => Mage::helper('tax')->__('State'),
                'values' => $regions
            )
        );

        /* FIXME!!! {*
        $fieldset->addField('tax_county_id', 'select',
            array(
                'name' => 'tax_county_id',
                'label' => Mage::helper('tax')->__('County'),
                'values' => array(
                    array(
                        'label' => '*',
                        'value' => ''
                    )
                ),
                'value' => $rateObject->getTaxCountyId()
            )
        );
        } */

        $fieldset->addField('zip_is_range', 'select', array(
            'name' => 'zip_is_range',
            'label' => Mage::helper('tax')->__('Zip/Post is Range'),
            'options'   => array(
                    '0' => Mage::helper('tax')->__('No'),
                    '1' => Mage::helper('tax')->__('Yes'),
                )
        ));

        if (!$rateObject->hasTaxPostcode()) {
            $rateObject->setTaxPostcode(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE));
        }

        $fieldset->addField('tax_postcode', 'text',
            array(
                'name' => 'tax_postcode',
                'label' => Mage::helper('tax')->__('Zip/Post Code'),
                'note' => Mage::helper('tax')->__("'*' - matches any; 'xyz*' - matches any that begins on 'xyz' and not longer than %d.", Mage::helper('tax')->getPostCodeSubStringLength()),
            )
        );

        $fieldset->addField('zip_from', 'text',
            array(
                'name' => 'zip_from',
                'label' => Mage::helper('tax')->__('Range From'),
                'value' => $rateObject->getZipFrom(),
                'required' => true,
                'class' => 'validate-digits'
            )
        );

        $fieldset->addField('zip_to', 'text',
            array(
                'name' => 'zip_to',
                'label' => Mage::helper('tax')->__('Range To'),
                'value' => $rateObject->getZipTo(),
                'required' => true,
                'class' => 'validate-digits'
            )
        );

        if ($rateObject->getRate()) {
            $value = 1*$rateObject->getRate();
        } else {
            $value = 0;
        }

        $fieldset->addField('rate', 'text',
            array(
                'name' => "rate",
                'label' => Mage::helper('tax')->__('Rate Percent'),
                'title' => Mage::helper('tax')->__('Rate Percent'),
                'value' => number_format($value, 4),
                'required' => true,
                'class' => 'validate-not-negative-number'
            )
        );

        $form->setAction($this->getUrl('*/tax_rate/save'));
        $form->setUseContainer(true);
        $form->setId('rate_form');
        $form->setMethod('post');

        if (!Mage::app()->isSingleStoreMode()) {
            $form->addElement(Mage::getBlockSingleton('adminhtml/tax_rate_title_fieldset')->setLegend(Mage::helper('tax')->__('Tax Titles')));
        }

        $form->setValues($rateObject->getData());
        $this->setForm($form);

        $this->setChild('form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("zip_is_range", 'zip_is_range')
            ->addFieldMap("tax_postcode", 'tax_postcode')
            ->addFieldMap("zip_from", 'zip_from')
            ->addFieldMap("zip_to", 'zip_to')
            ->addFieldDependence('zip_from', 'zip_is_range', '1')
            ->addFieldDependence('zip_to', 'zip_is_range', '1')
            ->addFieldDependence('tax_postcode', 'zip_is_range', '0')
        );

        return parent::_prepareForm();
    }
}
