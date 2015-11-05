<?php
class Web_Usps_Model_Observer
{
    public function validateAddress($observer)
    {
        $allowedActions = array('saveBilling','saveShipping');
        $actionName = Mage::app()->getRequest()->getActionName();
        if(!in_array($actionName,$allowedActions))
        {
            return;
        }
        $params = Mage::app()->getRequest()->getParams();
        $address = Mage::helper('web_usps')->assignAddress($params);
        if(!$address)
        {
            return;
        }
        $rs = Mage::helper('web_usps')->validate($address);
        $rsx = Zend_Json::decode($rs);
        $oR = Zend_Json::decode($rsx['skip_res']);
        unset($rsx['skip_res']);
        $rs = array_merge($oR,$rsx);
        Mage::app()->getResponse()->setBody(Zend_Json::encode($rs));
    }
}