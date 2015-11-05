<?php

class Unirgy_Dropship_Block_Vendor_Preferences extends Mage_Core_Block_Template
{
    public function getFieldsets()
    {
        $hlp = Mage::helper('udropship');

        $visible = Mage::getStoreConfig('udropship/vendor/visible_preferences');
        $visible = $visible ? explode(',', $visible) : false;

        $fieldsets = array();

        $fieldsets['account'] = array(
            'position' => 0,
            'legend' => 'Account Information',
            'fields' => array(
                'vendor_name' => array(
                    'position' => 1,
                    'name' => 'vendor_name',
                    'type' => 'text',
                    'label' => 'Vendor Name',
                ),
                'vendor_attn' => array(
                    'position' => 2,
                    'name' => 'vendor_attn',
                    'type' => 'text',
                    'label' => 'Attention To',
                ),
                'subdomain' => array(
                    'position' => 2,
                    'name' => 'subdomain',
                    'type' => 'text',
                    'label' => 'Subdomain',
                ),                
                'email' => array(
                    'position' => 3,
                    'name' => 'email',
                    'type' => 'text',
                    'label' => 'Email Address / Login',
                ),
                'password' => array(
                    'position' => 4,
                    'name' => 'password',
                    'type' => 'text',
                    'label' => 'Login Password',
                ),
                'telephone' => array(
                    'position' => 5,
                    'name' => 'telephone',
                    'type' => 'text',
                    'label' => 'Telephone',
                ),
            ),
        );

        $countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();

        $countryId = Mage::registry('vendor_data') ? Mage::registry('vendor_data')->getCountryId() : null;
        if (!$countryId) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }

        $regionCollection = Mage::getModel('directory/region')
            ->getCollection()
            ->addCountryFilter($countryId);

        $regions = $regionCollection->toOptionArray();

        if ($regions) {
            $regions[0]['label'] = $hlp->__('Please select state...');
        } else {
            $regions = array(array('value'=>'', 'label'=>''));
        }

        $fieldsets['shipping_origin'] = array(
            'position' => 1,
            'legend' => 'Shipping Origin Address',
            'fields' => array(
                'street' => array(
                    'position' => 1,
                    'name' => 'street',
                    'type' => 'textarea',
                    'label' => 'Street',
                ),
                'limit_zipcode' => array(
                    'position' => 1,
                    'name' => 'limit_zipcode',
                    'type' => 'textarea',
                    'label' => 'Zipcodes',
                ),
                'city' => array(
                    'position' => 2,
                    'name' => 'city',
                    'type' => 'text',
                    'label' => 'City',
                ),
                'zip' => array(
                    'position' => 3,
                    'name' => 'zip',
                    'type' => 'text',
                    'label' => 'Zip / Postal code',
                ),
                'country_id' => array(
                    'position' => 4,
                    'name' => 'country_id',
                    'type' => 'select',
                    'label' => 'Country',
                    'options' => $countries,
                ),
                'region_id' => array(
                    'position' => 5,
                    'name' => 'region_id',
                    'type' => 'select',
                    'label' => 'State',
                    'options' => $regions,
                ),
            ),
        );

        $_v = Mage::getSingleton('udropship/session')->getVendor();
        $hlp = Mage::helper('udropship');

        $fieldsets['delivery_time'] = array(
            'position' => 2,
            'legend' => 'Delivery Time',
            'fields' => array(
                'cut_off_time' => array(
                    'position' => 1,
                    'name' => 'cut_off_time',
                    'type' => 'text',
                    'label' => 'Cut Off Time',
								/*	'options'	=> array(
										array('label' => '12pm', 'value' => '0'),
										array('label' => '1am', 'value' => '1'),
										array('label' => '2am', 'value' => '2'),
										array('label' => '3am', 'value' => '3'),
										array('label' => '4am', 'value' => '4'),
										array('label' => '5am', 'value' => '5'),
										array('label' => '6am', 'value' => '6'),
										array('label' => '7am', 'value' => '7'),
										array('label' => '8am', 'value' => '8'),
										array('label' => '9am', 'value' => '9'),
										array('label' => '10am', 'value' => '10'),
										array('label' => '11am', 'value' => '11'),
										array('label' => '12am', 'value' => '12'),
										array('label' => '1pm', 'value' => '13'),
										array('label' => '2pm', 'value' => '14'),
										array('label' => '3pm', 'value' => '15'),
										array('label' => '4pm', 'value' => '16'),
										array('label' => '5pm', 'value' => '17'),
										array('label' => '6pm', 'value' => '18'),
										array('label' => '7pm', 'value' => '19'),
										array('label' => '8pm', 'value' => '20'),
										array('label' => '9pm', 'value' => '21'),
										array('label' => '10pm', 'value' => '22'),
										array('label' => '11pm', 'value' => '23'),
									),*/
                        'note' => 'It must be in (24 hour format). <br/>'
                ),
                'non_working_days' => array(
                    'position' => 2,
                    'name' => 'non_working_days',
                    'type' => 'multiselect',
                    'label' => 'Non Delivery Days',
                    'options'	=> array(
                        array('label' => 'Monday', 'value' => 'Monday'),
                        array('label' => 'Tuesday', 'value' => 'Tuesday'),
                        array('label' => 'Wednesday', 'value' => 'Wednesday'),
                        array('label' => 'Thursday', 'value' => 'Thursday'),
                        array('label' => 'Friday', 'value' => 'Friday'),
                        array('label' => 'Saturday', 'value' => 'Saturday'),
                        array('label' => 'Sunday', 'value' => 'Sunday'),
                    )
                ),
            ),
        );

        $fieldsets['delivery_charge'] = array(
            'position' => 3,
            'legend' => 'Delivery Charge',
            'fields' => array(
                'delivery_charge' => array(
                    'position' => 1,
                    'name' => 'delivery_charge',
                    'type' => 'text',
                    'label' => 'Delivery Charge'                   
                )
            )
        );
        
        $fieldsets['social_accounts'] = array(
            'position' => 4,
            'legend' => 'Google Reference',
            'fields' => array(
                'google_reference' => array(
                    'position' => 1,
                    'name' => 'google_reference',
                    'type' => 'text',
                    'label' => 'Google Reference ID'                   
                )
            )
        );
            

        Mage::dispatchEvent('udropship_vendor_front_preferences', array(
            'fieldsets'=>&$fieldsets
        ));

        uasort($fieldsets, array($hlp, 'usortByPosition'));
        foreach ($fieldsets as $k=>$v) {
            if (empty($v['fields'])) {
                continue;
            }
            uasort($v['fields'], array($hlp, 'usortByPosition'));
        }

        return $fieldsets;
    }
}