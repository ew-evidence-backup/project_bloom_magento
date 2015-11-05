<?php
class Bloomnation_Taxes_Model_Calculation extends Mage_Tax_Model_Calculation
{
    public function getRateRequest(
        $shippingAddress = null,
        $billingAddress = null,
        $customerTaxClass = null,
        $store = null)
    {

        if ($shippingAddress === false && $billingAddress === false && $customerTaxClass === false) {
            return $this->getRateOriginRequest($store);
        }
        $address    = new Varien_Object();
        $customer   = $this->getCustomer();
        $basedOn    = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);

        if (($shippingAddress === false && $basedOn == 'shipping')
            || ($billingAddress === false && $basedOn == 'billing')) {
            $basedOn = 'default';
        } else {
            if ((($billingAddress === false || is_null($billingAddress) || !$billingAddress->getCountryId())
                && $basedOn == 'billing')
                || (($shippingAddress === false || is_null($shippingAddress) || !$shippingAddress->getCountryId())
                    && $basedOn == 'shipping')
            ){
                if ($customer) {
                    $defBilling = $customer->getDefaultBillingAddress();
                    $defShipping = $customer->getDefaultShippingAddress();

                    if ($basedOn == 'billing' && $defBilling && $defBilling->getCountryId()) {
                        $billingAddress = $defBilling;
                    } else if ($basedOn == 'shipping' && $defShipping && $defShipping->getCountryId()) {
                        $shippingAddress = $defShipping;
                    } else {
                        $basedOn = 'default';
                    }
                } else {
                    $basedOn = 'default';
                }
            }
        }
        if(($billingAddress && $billingAddress ) && ( $billingAddress->getRegion() == 'California' ||
            $shippingAddress->getRegion() ==
            'California')){
            $customerTaxClass = 5;
        }
        switch ($basedOn) {
            case 'billing':
                $address = $billingAddress;
                break;
            case 'shipping':
                $address = $shippingAddress;
                break;
            case 'origin':
                $address = $this->getRateOriginRequest($store);
                break;
            case 'default':
                $address
                    ->setCountryId(Mage::getStoreConfig(
                    Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                    $store))
                    ->setRegionId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION, $store))
                    ->setPostcode(Mage::getStoreConfig(
                    Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE,
                    $store));
                break;
        }

        if (is_null($customerTaxClass) && $customer) {
            $customerTaxClass = $customer->getTaxClassId();
        } elseif (($customerTaxClass === false) || !$customer) {
            $customerTaxClass = $this->getDefaultCustomerTaxClass($store);
        }

        $request = new Varien_Object();
        $request
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setStore($store)
            ->setCustomerClassId($customerTaxClass);
        return $request;
    }
}