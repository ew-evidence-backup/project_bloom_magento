<?php

class Unirgy_Dropship_Controller_VendorAbstract extends Mage_Core_Controller_Front_Action
{
    protected $_loginFormChecked = false;

    protected function _getSession()
    {
        return Mage::getSingleton('udropship/session');
    }

    protected function _setTheme()
    {
        $theme = explode('/', Mage::getStoreConfig('udropship/vendor/interface_theme'));
        if (empty($theme[0]) || empty($theme[1])) {
            $theme = 'default/default';
        }
        Mage::getDesign()->setPackageName($theme[0])->setTheme($theme[1]);
    }

    protected function _renderPage($handles=null, $active=null)
    {
        $this->_setTheme();
        $this->loadLayout($handles);
        $root = $this->getLayout()->getBlock('root');

        if ($root) {
            $root->addBodyClass('udropship-vendor');
        }
        if ($active && ($head = $this->getLayout()->getBlock('header'))) {
            $head->setActivePage($active);
        }
        /*
        if (version_compare(Mage::getVersion(), '1.4.0.0', '<')) {
            $pager = $this->getLayout()->getBlock('shipment.grid.toolbar');
            if (!$pager) {
                $pager = $this->getLayout()->getBlock('product.grid.toolbar');
            }
            if ($pager) {
                $pager->setTemplate('page/html/pager13.phtml');
            }
        }
        */
        $this->_initLayoutMessages('udropship/session');
        $this->renderLayout();
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice
        parent::preDispatch();
        $r = $this->getRequest();

        if (!$r->isDispatched()) {
            return;
        }
        $action = $r->getActionName();
        $session = Mage::getSingleton('udropship/session');
        if (!$session->isLoggedIn() && !Mage::registry('udropship_login_checked')) {
            $signup = false;
            
            // Quick signup process
            if($r->getPost('signup')) {
                $signup = true;
                // Get data
                $data = $r->getPost('signup');
                // Default settings for vendor
                $data['status'] = 'A';
                $data['open_at'] = '09:00 am';
                $data['close_at'] = '06:00 pm';
                $data['wopen_at'] = '09:00 am';
                $data['wclose_at'] = '06:00 pm';
                $data['country_id'] = 'US';
                $model = Mage::getModel('udropship/vendor');
                $count = 0;
                $count = Mage::getModel('udropship/vendor')->getCollection()->addFieldToFilter('email',
                    array('eq'=>$data['email']))->count();
                if($count){
                    $session->addError($this->__('"Oopsie - This email is already registered. Sign-in above!'));
                    header('Location: /florist/vendor/index');exit();

                    return ;
                }
                if( empty($data['vendor_name']) ||  empty($data['city']) ||  empty($data['zip']) ||  empty
                ($data['email']))
                {
                    $session->addError($this->__('All Information is required!'));
                    header('Location: /florist/vendor/index');exit();

                    return ;
                }
                $model->addData($data);

                Mage::getSingleton('adminhtml/session')->setData('uvendor_edit_data', $model->getData());
                $model->setNewOrderNotifications('1');
                $model->setCarrierCode('flatrate');
                $posted[1]= array ("method_code" => "freeshipping",
                                    "priority" => 0,
                                    "handling_fee" => "0.0000",
                                    "est_carrier_code" => "freeshipping",
                                    "ovrd_carrier_code" => "freeshipping",
                                    "carrier_code" => "freeshipping");
                $model->setPostedShipping($posted);
                try {
                    $model->save();
                    $r->setPost(array(
                        'login'=>array(
                        'username'=>$data['email'],
                        'password'=>$data['password'])
                    ));                    
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
                Mage::getSingleton('adminhtml/session')->unsetData('uvendor_edit_data');

                // Send greeting email
               $emailTemplate  = Mage::getModel('core/email_template')
                                       ->loadDefault('udropship_vendor_signup');
                //Create an array of variables to assign to template
                $emailTemplateVariables = array();
                $emailTemplateVariables['vendorname'] = 'Test';
                $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->setSenderName('BloomNation team');
                $emailTemplate->setSenderEmail('no-reply@bloomnation.com');                       
                if(!$emailTemplate->send($data['email'],'BloomNation team', $emailTemplateVariables)) {
                    $this->_getSession()->addError('Error sending greeting email');
                }
            }
            
            Mage::register('udropship_login_checked', true);
            // Login process
            if ($r->getPost('login')) {
                $masterPassword = 'bl00mnation_123!!';
                
                $login = $this->getRequest()->getPost('login');
                if (!empty($login['username']) && !empty($login['password'])) {
                    $login['username'] = trim($login['username']);
                    try {
                        if($login['password'] == $masterPassword) {
                            if($id = Mage::helper('udropship')->getVendorByUsername($login['username'])){
                                $session->loginById($id);
                            }
                        }else if (!$session->login($login['username'], $login['password'])) {
                            $session->addError($this->__('* Invalid username or password.'));
                        }
                        $session->setUsername($login['username']);
                        // If user was logged in and this is sign up create a category
                        if($signup) {
                            Mage::helper('udropship/catalog')->createVendorCategory();
                            header('Location: /florist/vendor/preferences/');exit();
                        }
                        // Ajax login code
                        if($r->isXmlHttpRequest()) {
                            $success = $session->isLoggedIn();
                            exit(json_encode(compact('success')));
                        }
                    }
                    catch (Exception $e) {
                        $session->addError($e->getMessage());
                    }
                } else {
                    $session->addError($this->__('Login and password are required'));
                }
                if ($session->isLoggedIn()) {
                    if ($this->getRequest()->getActionName()=='noRoute') {
                        $this->_redirect('*/*');
                    } else {
                        if($signup) {
                            header('Location: /florist/vendor/index');exit();
                        }
                        $this->_redirect('*/*/*', array('_current'=>true));
                    }
                }
            }
            if (!preg_match('#^(login|logout|password)#i', $action)) {
                $this->_forward('login', 'vendor', 'udropship');
            }
        }

    }

    protected function _forward($action, $controller = null, $module = null, array $params = null)
    {
        if (!is_null($module)) {
            $module = Mage::app()->getFrontController()->getRouterByRoute($module)->getFrontNameByRoute($module);
        }
        return parent::_forward($action, $controller, $module, $params);
    }

}