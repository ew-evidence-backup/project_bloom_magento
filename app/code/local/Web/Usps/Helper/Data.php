<?php
class Web_Usps_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isModEnabled()
    {
        return Mage::getStoreConfig('usps/config/usps_address_enabled');
    }
    public function getAccountNumber()
    {
        return Mage::getStoreConfig('usps/config/account_number');
    }
    public function getVerifyInstance()
    {
        if($this->isModEnabled() && $this->getAccountNumber())
        {
            return new Usps_USPSAddressVerify($this->getAccountNumber());
        }
    }
    public function allowedCountry($country = '')
    {
        $configArray = explode(',',Mage::getStoreConfig('usps/config/allowed_country'));
        if(in_array($country,$configArray))
        {
            return true;
        }
        return false;
    }
    public function isAddress($data = array())
    {
        if(!empty($data['country_id']) && !empty($data['postcode']) && !empty($data['region_id']) && !empty
        ($data['address1']))
        {
            return true;
        }
        return false;
    }
    public function assignAddress($data = array())
    {

        if(!isset($data['billing']) && !isset($data['shipping']) && !$this->isAddress($data))
        {return false;}
        if($this->isAddress($data))
        {
            $data['street'][0] = $data['address1'];
            $data['street'][1] = $data['address2'];
            $addressData = $data;
        }else{
            $addressData = isset($data['billing']) ? $data['billing'] : $data['shipping'];
        }
        if(!$this->allowedCountry($addressData['country_id'])){
            return false;
        }
        $zip = explode('-',$addressData['postcode']);
        $region = Mage::getModel('directory/region')->load($addressData['region_id']);
        $street1 = preg_replace('/#\S+/i','',$addressData['street']['0'].' '.$addressData['street']['1']);
        $address = new Usps_USPSAddress;
        $address->setFirmName(!empty($addressData['company']) ? $addressData['company'] : 'Apartment' );
        $address->setApt($addressData['street']['0']);
        $address->setAddress($street1);
        $address->setCity($addressData['city']);
        $address->setState($region->getCode());
        $address->setZip5($zip[0]);
        $address->setZip4(isset($zip[1])? $zip[1] : '' );
        return $address;
    }
    public function compareAddress($address1,$address2)
    {
        $rs = true;
        if(strtolower($address1['City']) != strtolower($address2['City'])){
            $rs = false;
        }
        if(strtolower($address1['State']) != strtolower($address2['State'])){
            $rs = false;
        }
        if($address1['Zip5'] != $address2['Zip5']){
            $rs = false;
        }
        if($address1['Zip4'] != $address2['Zip4']){
            //$rs = false;
        }
        return $rs;
    }
    public function validate($address)
    {
        $result = array();
        $verify = $this->getVerifyInstance();
        $verify->addAddress($address);
        $verify->verify();
        $res = $verify->getArrayResponse();
        $result['skip_res'] = Mage::app()->getResponse()->getBody();

        if(!$verify->isSuccess())
        {
            //$result['error'] = 1;
            //$result['message'] = $this->__($verify->getErrorMessage());
            $result['notice']['message'] = $res['AddressValidateResponse']['Address']['Error']['Description'];
            $result['notice']['message'] .= "";
            $result['notice']['method'] = Mage::app()->getRequest()->getActionName();
            return Zend_Json::encode($result);
        }


        //$result['error'] = 1;
        //$result['message'] = 'error';

        $r = $res['AddressValidateResponse']['Address'];
        $newAddress['Address1'] = $r['Address1'];
        $newAddress['Address2'] = $r['Address2'];
        $newAddress['City'] = $r['City'];
        $newAddress['State'] = $r['State'];
        $newAddress['Zip5'] = $r['Zip5'];
        $newAddress['Zip4'] = $r['Zip4'];
        $newAddress['method'] = Mage::app()->getRequest()->getActionName();


        if(!$this->compareAddress($newAddress,$address->getAddressInfo()))
        {
            $newAddress['message'] = $this->__("Address might be wrong!?");
        }else{

        }
        $result['newAddress'] = $newAddress;
        return Zend_Json::encode($result);




    }
    public function printJs()
    {
        if($this->isModEnabled() && $this->isModuleEnabled()){
            return '<script type="text/javascript" src="'.Mage::getDesign()->getSkinUrl('js/usps_checkout.js')
                .'"></script>
                ';
        }
        return '';
    }

}