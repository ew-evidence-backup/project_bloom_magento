<?php
class Unirgy_Dropship_ThemeController extends Unirgy_Dropship_Controller_VendorAbstract {
    
    public function indexAction() {
        $this->_renderPage();
    }

    public function selectAction() {
        $this->_renderPage();
    }
    
    public function invitationAction() {
        $validCodes = array(
            'aaaa'
        );
        
        if($post = $this->getRequest()->getPost()) {
            $success = false;
            $msg = '';
            // Check inviatation code
            $invCode = $this->getRequest()->getParam('invitation_code', false);
            if($invCode && in_array($invCode,$validCodes)) {
                $success = true;
            }
            
            exit(json_encode(compact('success','msg')));
        }
    }
    
    public function checknameAction() {
        $success = false;
        $msg = '';
        
        if($post = $this->getRequest()->getPost()) {
            if($name = $post['name']) {
                $query = "SELECT vendor_id FROM udropship_vendor WHERE  subdomain = '$name'";
                $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
                if(!$result) {
                    $success = true;
                }
            }
        }
        
        exit(json_encode(compact('success','msg')));
    }
    
    public function domainnameAction() {
        $v = $this->_getSession()->getVendor();
        $domain = $this->getRequest()->getParam('domain');
        
        $to      = 'info@bloomnation.com';
        $subject = 'Domain name request by '.$v->getVendorName;
        $message = $v->getVendorName.' '.$v->getEmail().' requests domain DNS change for :'.$domain;
        $headers = 'From: ' .$v->getEmail(). "\r\n" .
            'Reply-To: ' .$v->getEmail(). "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $message, $headers);
        exit();
    }
}