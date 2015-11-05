<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_Dropship
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

class Unirgy_Dropship_VendorController extends Unirgy_Dropship_Controller_VendorAbstract
{

    public function indexAction() {
        if(isset($_GET['welcome'])) { Mage::register('reg-complete', 'reg-complete'); }
        $this->_renderPage(null, 'dashboard');
    }

    public function ordersAction()
    {
        $_hlp = Mage::helper('udropship');
        if ($_hlp->isUdpoActive() && !$this instanceof Unirgy_DropshipPo_VendorController) {
            $this->_forward('index', 'vendor', 'udpo');
            return;
        }
        switch ($this->getRequest()->getParam('submit_action')) {
        case 'labelBatch':
        case $_hlp->__('Create and Download Labels Batch'):
            $this->_forward('labelBatch');
            return;

        case 'existingLabelBatch':
            $this->_forward('existingLabelBatch');
            return;

        case 'packingSlips':
        case $_hlp->__('Download Packing Slips'):
            $this->_forward('packingSlips');
            return;

        case 'updateShipmentsStatus':
            $this->_forward('updateShipmentsStatus');
            return;
        }

        $this->_renderPage(null, 'orders');
    }

    public function loginAction()
    {
        if (Mage::getSingleton('udropship/session')->isLoggedIn()) {
            $this->_forward('index');
        } else {
        $ajax = $this->getRequest()->getParam('ajax');
        if ($ajax) {
            Mage::getSingleton('udropship/session')->addError($this->__('Your session has been expired. Please log in again.'));
        }
        $this->_renderPage($ajax ? 'udropship_vendor_login_ajax' : null);
    }
    }

    public function logoutAction()
    {
        $this->_getSession()->logout();
        $this->_redirect('udropship/vendor');
    }

    public function passwordAction()
    {
        $session = Mage::getSingleton('udropship/session');
        $hlp = Mage::helper('udropship');
        $confirm = $this->getRequest()->getParam('confirm');
        if ($confirm) {
            $vendor = Mage::getModel('udropship/vendor')->load($confirm, 'random_hash');
            if ($vendor->getId()) {
                Mage::register('reset_vendor', $vendor);
            } else {
                $session->addError($hlp->__('Invalid confirmation link'));
            }
        }
        $this->_renderPage();
    }

    public function passwordPostAction()
    {
        $session = Mage::getSingleton('udropship/session');
        $hlp = Mage::helper('udropship');
        try {
            $r = $this->getRequest();
            if (($email = $r->getParam('email'))) {
                $hlp->sendPasswordResetEmail($email);
                $session->addSuccess($hlp->__('Thank you, password reset instructions have been sent to the email you have provided, if a vendor with such email exists.'));
                $this->_redirect('*/*/login');
            } elseif (($confirm = $r->getParam('confirm'))) {
                $password = $r->getParam('password');
                $passwordConfirm = $r->getParam('password_confirm');
                $vendor = Mage::getModel('udropship/vendor')->load($confirm, 'random_hash');
                if (!$password || !$passwordConfirm || $password!=$passwordConfirm || !$vendor->getId()) {
                    $session->addError('Invalid form data');
                    $this->_redirect('*/*/password', array('confirm'=>$confirm));
                    return;
                }
                $vendor->setPassword($password)->unsRandomHash()->save();
                $session->loginById($vendor->getId());
                $session->addSuccess($hlp->__('Your password has been reset.'));
                $this->_redirect('*/*');
            } else {
                $session->addError($hlp->__('Invalid form data'));
                $this->_redirect('*/*/password');
            }
        } catch (Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirect('*/*/password');
        }
    }

    public function accountAction()
    {
        $this->_renderPage();
    }

    public function preferencesAction()
    {
        $this->_renderPage(null, 'preferences');
    }

    public function preferencesPostAction()
    {
        $defaultAllowedTags = Mage::getStoreConfig('udropship/vendor/preferences_allowed_tags');
        $session = Mage::getSingleton('udropship/session');
        $hlp = Mage::helper('udropship');
        $r = $this->getRequest();
        if ($r->isPost()) {
            $p = $r->getPost();
            $hlp->processPostMultiselects($p);
            // File uploads for design config
            foreach($_FILES as $key=>$file) {
                if($file['size'] > 0) {
                    if('custom_slider_image' == $key || 'custom_background' == $key) {
                        $allowedExt = array('jpeg','jpg','png');
                        $nameParts = explode('.', $file['name']);
                        if(!in_array($nameParts[1], $allowedExt)) {
                            exit('not allowed file extension');
                        }
                        $newName = md5(mt_rand(0,90001)).'.'.$nameParts[1];
                        $newPath = './media/vendor/'.$session->getId().'/'.$newName;
                        if(@move_uploaded_file($file['tmp_name'], $newPath)) {
                            unset($_FILES[$key]);
                        }
                        if('custom_slider_image' == $key) {
                            // Remove first symbol not ot include dot
                            $p['design_config']['slider']['slides'][0]['image'] = substr($newPath,1);
                        }
                        if('custom_background' == $key) {
                            // Remove first symbol not ot include dot
                            $p['design_config']['appearance']['content_bg_img'] = substr($newPath,1);                        
                        }
                    }
                }
            }
            // Design config
            $p['design_config'] = json_encode($p['design_config']);
            
            // Merging of the strings required for comfortable input
            $p['telephone'] = preg_replace('/\D/','',$p['phone_1'].$p['phone_2'].$p['phone_3']);
            $p['fax'] = preg_replace('/\D/','',$p['fax_1'].$p['fax_2'].$p['fax_3']);
            $p['vendor_attn'] = preg_replace('/\W/','',$p['attn_1']).' '. preg_replace('/\W/','',$p['attn_2']);
            if('1' == $p['submitNewImg']) {
                $p['logo'] = $p['new_image'];
            }
            if(isset($p['url_key'])) {
                preg_match('/biz\/(.*)/',$p['url_key'],$matches);
                $p['url_key'] = ($matches[1]) ? $matches[1] : '';
            }
            if(isset($p['cut_off_time']) && is_array($p['cut_off_time'])) {
                $p['cut_off_time'] = $p['cut_off_time']['hours'].':'.$p['cut_off_time']['minutes'].':01 '.$p['cut_off_time']['p'];
                $p['cut_off_time'] = date('H:i:s',strtotime($p['cut_off_time']));
            }
            if(isset($p['open_at']) && is_array($p['open_at'])) {
                $p['open_at'] = $p['open_at']['hours'].':'.$p['open_at']['minutes'].' '.$p['open_at']['p'];
                $p['open_at'] = date('H:i:s',strtotime($p['open_at']));
            }
            if(isset($p['close_at']) && is_array($p['close_at'])) {
                $p['close_at'] = $p['close_at']['hours'].':'.$p['close_at']['minutes'].' '.$p['close_at']['p'];
                $p['close_at'] = date('H:i:s',strtotime($p['close_at']));
            }
            if(isset($p['wopen_at']) && is_array($p['wopen_at'])) {
                $p['wopen_at'] = $p['wopen_at']['hours'].':'.$p['wopen_at']['minutes'].' '.$p['wopen_at']['p'];
                $p['wopen_at'] = date('H:i:s',strtotime($p['wopen_at']));
            }
            if(isset($p['wclose_at']) && is_array($p['wclose_at'])) {
                $p['wclose_at'] = $p['wclose_at']['hours'].':'.$p['wclose_at']['minutes'].' '.$p['wclose_at']['p'];
                $p['wclose_at'] = date('H:i:s',strtotime($p['wclose_at']));
            }
            // Store hours logic, don't allow open to be later then closed
            if (
               (strtotime($p['open_at']) > strtotime($p['close_at']) ) ||
               (strtotime($p['wopen_at']) > strtotime($p['wclose_at']) )
              ) {
                $session->addError('Store open hours must be earlier then store close hours');
                header('Location: '.Mage::getUrl('udropship/vendor/preferences').'#hoursForm');exit();
            }
            
            // Filter zipcodes input
            if(isset($p['limit_zipcode']) && !empty($p['limit_zipcode'])) {
                // Standardize the format
                $limitZipcode = preg_split("/[\s,]+/", $p['limit_zipcode']);
                foreach($limitZipcode as $i=>$v) {
                    $limitZipcode[$i] = trim($v);
                    if(strlen($v) != 5) {
                        unset($limitZipcode[$i]);
                        $session->addError("'$v' was not included because it was not in 5 digit format (ex-90210) <br/>");
                    }
                }
                $p['limit_zipcode'] = implode("\n", $limitZipcode);
            }
            
            try {
                $v = $session->getVendor();

                foreach (array(
                    'vendor_name', 'vendor_attn',
                    'email', 'password',
                    'telephone', 'fax',
                    'street', 'city', 'zip', 'country_id', 'region_id',
                    'cut_off_time',
                    'delivery_charge',
                    'subdomain', 'google_reference', 'vendor_website', 'has_same_day_delivery','facebook_page_id',
                    'florist_type','about_owner', 'owner_photo', 'dozen_rose_price','open_at','close_at', 'v_timezone',
                    'wopen_at', 'wclose_at',
                    'facebook_page',
                    'design_config'
                ) as $f) {
                    $p[$f] = trim($p[$f]);
                    if(!empty($p[$f])) {
                        $v->setData($f, $p[$f]);
                    }
                }

                if(isset($p['non_delivery_dates']) && !empty($p['non_delivery_dates'])) {
                    Mage::helper('udropship')->removeDates($v->getId());
                    Mage::helper('udropship')->addDates($v->getId(), $p['non_delivery_dates']);
                }
                else {
                    Mage::helper('udropship')->removeDates($v->getId());
                }

                if(isset($p['non_working_days']) && !empty($p['non_working_days'])) {
                    Mage::helper('udropship')->removeDays($v->getId());
                    Mage::helper('udropship')->addDays($v->getId(), $p['non_working_days']);
                }
                else {
                    Mage::helper('udropship')->removeDays($v->getId());
                }                

                foreach (Mage::getConfig()->getNode('global/udropship/vendor/fields')->children() as $code=>$node) {
                    if (!isset($p[$code])) {
                        continue;
                    }
                    $param = $p[$code];
                    if (is_array($param)) {
                        foreach ($param as $key=>$val) {
                            $param[$key] = strip_tags($val, $defaultAllowedTags);
                        }
                    }
                    else {
                        $allowedTags = $defaultAllowedTags;
                        if ($node->filter_input && ($stripTags = $node->filter_input->strip_tags) && isset($stripTags->allowed)) {
                            $allowedTags = (string)$node->strip_tags->allowed;
                        }
                        if ($allowedTags && $node->type != 'wysiwyg') {
                            $param = strip_tags($param, $allowedTags);
                        }

                        if ($node->filter_input && ($replace = $node->filter_input->preg_replace) && isset($replace->from) && isset($replace->to)) {
                            $param = preg_replace((string)$replace->from, (string)$replace->to, $param);
                        }
                    } // end code injection protection
                    $v->setData($code, $param);
                }
                // Custom vars combined or About Us saving
                if(isset($p['about_us'])) {
                    $customDataCombined = '===== about_us ====='."\n";
                    $customDataCombined.= $p['about_us'];
                    $v->setData('custom_data_combined',$customDataCombined);
                }
                
                Mage::dispatchEvent('udropship_vendor_preferences_save_before', array('vendor'=>$v, 'post_data'=>&$p));
                $v->save();
                #echo "<pre>"; print_r($v->debug()); exit;
                if(preg_match('/theme/',$_SERVER['HTTP_REFERER'])) {
                    $session->addSuccess('Settings have been saved. <a href="//'.$v->getSubdomain().'.bloomnation.com" target="_blank">I want to see the changes now!</a>');
                } else {
                    $session->addSuccess('Settings have been saved.');
                }
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        }
        // Dont return HTML on Ajax Requests
        if($this->getRequest()->isXmlHttpRequest()) {
            $msg = array();
            exit(json_encode(compact('msg')));
        }
        if(preg_match('/storefront/',$_SERVER['HTTP_REFERER'])) {
            $this->_redirect('udropship/vendor/storefront');
        } elseif(preg_match('/theme/',$_SERVER['HTTP_REFERER'])) {
            $this->_redirect('udropship/theme/index');
        } else {
            $this->_redirect('udropship/vendor/preferences');
        }
    }

    public function productAction()
    {
        $this->_renderPage(null, 'stockprice');
    }

    public function productSaveAction()
    {
        $hlp = Mage::helper('udropship');
        $session = Mage::getSingleton('udropship/session');
        try {
            $cnt = $hlp->saveVendorProducts($this->getRequest()->getParam('vp'));
            if (($multi = Mage::getConfig()->getNode('modules/Unirgy_DropshipMulti')) && $multi->is('active')) {
                $cnt += Mage::helper('udmulti')->saveVendorProductsPidKeys($this->getRequest()->getParam('vp'));
            }
            if ($cnt) {
                $session->addSuccess($hlp->__($cnt==1 ? '%s product was updated' : '%s products were updated', $cnt));
            } else {
                $session->addNotice($hlp->__('No updates were made'));
            }
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }
        if (is_callable(array(Mage::helper('core/http', 'getHttpReferer')))) {
            $this->getResponse()->setRedirect(Mage::helper('core/http')->getHttpReferer());
        } else {
            $this->getResponse()->setRedirect(@$_SERVER['HTTP_REFERER']);
        }
    }

    public function batchesAction()
    {
        $this->_renderPage(null, 'batches');
    }

    public function shipmentInfoAction()
    {
        $this->_setTheme();
        $this->loadLayout(false);

        $block = $this->getLayout()->getBlock('info');
        Mage::helper('udropship')->applyItemRenderers('sales_order_shipment', $block, '/checkout/', false);
        if (($url = Mage::registry('udropship_download_url'))) {
            $block->setDownloadUrl($url);
        }
        $this->_initLayoutMessages('udropship/session');

        $this->getResponse()->setBody($block->toHtml());
    }

    public function shipmentPostAction()
    {
        $hlp = Mage::helper('udropship');
        $r = $this->getRequest();
        $id = $r->getParam('id');
        $shipment = Mage::getModel('sales/order_shipment')->load($id);
        $vendor = $hlp->getVendor($shipment->getUdropshipVendor());
        $session = $this->_getSession();

        if (!$shipment->getId()) {
            return;
        }

        try {
            $store = $shipment->getOrder()->getStore();

            $track = null;
            $highlight = array();

            $partial = $r->getParam('partial_availability');
            $partialQty = $r->getParam('partial_qty');

            $printLabel = $r->getParam('print_label');
            $number = $r->getParam('tracking_id');

            $notifyOn = Mage::getStoreConfig('udropship/customer/notify_on', $store);
            $pollTracking = Mage::getStoreConfig('udropship/customer/poll_tracking', $store);
            $autoComplete = Mage::getStoreConfig('udropship/vendor/auto_shipment_complete', $store);

            $statusShipped = Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_SHIPPED;
            $statusDelivered = Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED;
            $statusCanceled = Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_CANCELED;
            $statuses = Mage::getSingleton('udropship/source')->setPath('shipment_statuses')->toOptionHash();
            // if label was printed
            if ($printLabel) {
                $status = $r->getParam('is_shipped') ? $statusShipped : Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_PARTIAL;
                $isShipped = $r->getParam('is_shipped') ? true : false;
            } else { // if status was set manually
                $status = $r->getParam('status');
                $isShipped = $status == $statusShipped || $status==$statusDelivered || $autoComplete && ($status==='' || is_null($status));
            }

            // if label to be printed
            if ($printLabel) {
                $data = array(
                    'weight'    => $r->getParam('weight'),
                    'value'     => $r->getParam('value'),
                    'length'    => $r->getParam('length'),
                    'width'     => $r->getParam('width'),
                    'height'    => $r->getParam('height'),
                    'reference' => $r->getParam('reference'),
                    'package_count' => $r->getParam('package_count'),
                );

                $extraLblInfo = $r->getParam('extra_label_info');
                $extraLblInfo = is_array($extraLblInfo) ? $extraLblInfo : array();
                $data = array_merge($data, $extraLblInfo);

                $oldUdropshipMethod = $shipment->getUdropshipMethod();
                $oldUdropshipMethodDesc = $shipment->getUdropshipMethodDescription();
                if ($r->getParam('use_method_code')) {
                    list($useCarrier, $useMethod) = explode('_', $r->getParam('use_method_code'), 2);
                    if (!empty($useCarrier) && !empty($useMethod)) {
                        $shipment->setUdropshipMethod($r->getParam('use_method_code'));
                        $carrierMethods = Mage::helper('udropship')->getCarrierMethods($useCarrier);
                        $shipment->setUdropshipMethodDescription(
                            Mage::getStoreConfig('carriers/'.$useCarrier.'/title', $shipment->getOrder()->getStoreId())
                            .' - '.$carrierMethods[$useMethod]
                        );
                    }
                }

                // generate label
                $batch = Mage::getModel('udropship/label_batch')
                    ->setVendor(Mage::getSingleton('udropship/session')->getVendor())
                    ->processShipments(array($shipment), $data, array('mark_shipped'=>$isShipped));

                // if batch of 1 label is successfull
                if ($batch->getShipmentCnt()) {
                    $url = Mage::getUrl('udropship/vendor/reprintLabelBatch', array('batch_id'=>$batch->getId()));
                    Mage::register('udropship_download_url', $url);

                    if (($track = $batch->getLastTrack())) {
                        $session->addSuccess('Label was succesfully created');
                        Mage::helper('udropship')->addShipmentComment(
                            $shipment,
                            $this->__('%s printed label ID %s', $vendor->getVendorName(), $track->getNumber())
                        );
                        $shipment->save();
                        $highlight['tracking'] = true;
                    }
                    if ($r->getParam('use_method_code')) {
                        $shipment->setUdropshipMethod($oldUdropshipMethod);
                        $shipment->setUdropshipMethodDescription($oldUdropshipMethodDesc);
                        $shipment->getResource()->saveAttribute($shipment, 'udropship_method');
                        $shipment->getResource()->saveAttribute($shipment, 'udropship_method_description');
                    }
                } else {
                    if ($batch->getErrors()) {
                        foreach ($batch->getErrors() as $error=>$cnt) {
                            $session->addError($hlp->__($error, $cnt));
                        }
                        if ($r->getParam('use_method_code')) {
                            $shipment->setUdropshipMethod($oldUdropshipMethod);
                            $shipment->setUdropshipMethodDescription($oldUdropshipMethodDesc);
                            $shipment->getResource()->saveAttribute($shipment, 'udropship_method');
                            $shipment->getResource()->saveAttribute($shipment, 'udropship_method_description');
                        }
                    } else {
                        $session->addError('No items are available for shipment');
                        if ($r->getParam('use_method_code')) {
                            $shipment->setUdropshipMethod($oldUdropshipMethod);
                            $shipment->setUdropshipMethodDescription($oldUdropshipMethodDesc);
                            $shipment->getResource()->saveAttribute($shipment, 'udropship_method');
                            $shipment->getResource()->saveAttribute($shipment, 'udropship_method_description');
                        }
                    }
                }

            } elseif ($number) { // if tracking id was added manually
                $method = explode('_', $shipment->getUdropshipMethod(), 2);
                $title = Mage::getStoreConfig('carriers/'.$method[0].'/title', $store);
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($number)
                    ->setCarrierCode($method[0])
                    ->setTitle($title);

                $shipment->addTrack($track);

                Mage::helper('udropship')->processTrackStatus($track, true, $isShipped);

                Mage::helper('udropship')->addShipmentComment(
                    $shipment,
                    $this->__('%s added tracking ID %s', $vendor->getVendorName(), $number)
                );
                $shipment->save();
                $session->addSuccess($this->__('Tracking ID has been added'));

                $highlight['tracking'] = true;
            }

            // if track was generated - for both label and manual tracking id
            /*
            if ($track) {
                // if poll tracking is enabled for the vendor
                if ($pollTracking && $vendor->getTrackApi()) {
                    $track->setUdropshipStatus(Unirgy_Dropship_Model_Source::TRACK_STATUS_PENDING);
                    $isShipped = false;
                } else { // otherwise process track
                    $track->setUdropshipStatus(Unirgy_Dropship_Model_Source::TRACK_STATUS_READY);
                    Mage::helper('udropship')->processTrackStatus($track, true, $isShipped);
                }
            */
            // if tracking id added manually and new status is not current status
            $shipmentStatuses = false;
            if (Mage::getStoreConfig('udropship/vendor/is_restrict_shipment_status')) {
                $shipmentStatuses = Mage::getStoreConfig('udropship/vendor/restrict_shipment_status');
                if (!is_array($shipmentStatuses)) {
                    $shipmentStatuses = explode(',', $shipmentStatuses);
                }
            }
            if (!$printLabel && !is_null($status) && $status!=='' && $status!=$shipment->getUdropshipStatus()
                && (!$shipmentStatuses || (in_array($shipment->getUdropshipStatus(), $shipmentStatuses) && in_array($status, $shipmentStatuses)))
            ) {
                $oldStatus = $shipment->getUdropshipStatus();
                if (($oldStatus==$statusShipped || $oldStatus==$statusDelivered)
                    && $status!=$statusShipped && $status!=$statusDelivered && $hlp->isUdpoActive()
                ) {
                    Mage::helper('udpo')->revertCompleteShipment($shipment, true);
                } elseif ($oldStatus==$statusCanceled && $hlp->isUdpoActive()) {
                    Mage::throwException(Mage::helper('udpo')->__('Canceled shipment cannot be reverted'));
                }
                $changedComment = $this->__('%s has changed the shipment status to %s', $vendor->getVendorName(), $statuses[$status]);
                $triedToChangeComment = $this->__('%s tried to change the shipment status to %s', $vendor->getVendorName(), $statuses[$status]);
                if ($status==$statusShipped || $status==$statusDelivered) {
                    $hlp->completeShipment($shipment, true, $status==$statusDelivered);
                    $hlp->completeOrderIfShipped($shipment, true);
                    $hlp->completeUdpoIfShipped($shipment, true);
                    Mage::helper('udropship')->addShipmentComment(
                        $shipment,
                        $changedComment
                    );
                } elseif ($status == $statusCanceled && $hlp->isUdpoActive()) {
                    if (Mage::helper('udpo')->cancelShipment($shipment, true)) {
                        Mage::helper('udropship')->addShipmentComment(
                            $shipment,
                            $changedComment
                        );
                        Mage::helper('udpo')->processPoStatusSave(Mage::helper('udpo')->getShipmentPo($shipment), Unirgy_DropshipPo_Model_Source::UDPO_STATUS_PARTIAL, true, $vendor);
                    } else {
                        Mage::helper('udropship')->addShipmentComment(
                            $shipment,
                            $triedToChangeComment
                        );
                    }
                } else {
                    $shipment->setUdropshipStatus($status)->save();
                    Mage::helper('udropship')->addShipmentComment(
                        $shipment,
                        $changedComment
                    );
                }
                $shipment->getCommentsCollection()->save();
                $session->addSuccess($this->__('Shipment status has been changed'));
            }

            $comment = $r->getParam('comment');
            if ($comment || $partial=='inform' && $partialQty) {
                if ($partialQty) {
                    $comment .= "\n\nPartial Availability:\n";
                    foreach ($shipment->getAllItems() as $item) {
                        if (!array_key_exists($item->getId(), $partialQty) || '' === $partialQty[$item->getId()]) {
                            continue;
                        }
                        $comment .= $this->__('%s x [%s] %s', $partialQty[$item->getId()], $item->getName(), $item->getSku())."\n";
                    }
                }

                Mage::helper('udropship')->sendVendorComment($shipment, $comment);
                $session->addSuccess($this->__('Your comment has been sent to store administrator'));

                $highlight['comment'] = true;
            }

            $deleteTrack = $r->getParam('delete_track');
            if ($deleteTrack) {
                $track = Mage::getModel('sales/order_shipment_track')->load($deleteTrack);
                if ($track->getId()) {

                    try {
                        $labelModel = Mage::helper('udropship')->getLabelCarrierInstance($track->getCarrierCode())->setVendor($vendor);
                        try {
                            $labelModel->voidLabel($track);
                            Mage::helper('udropship')->addShipmentComment(
                                $shipment,
                                $this->__('% voided tracking ID %s', $vendor->getVendorName(), $track->getNumber())
                            );
                            $session->addSuccess($this->__('Track %s was voided', $track->getNumber()));
                        } catch (Exception $e) {
                            Mage::helper('udropship')->addShipmentComment(
                                $shipment,
                                $this->__('% attempted to void tracking ID %s: %s', $vendor->getVendorName(), $track->getNumber(), $e->getMessage())
                            );
                            $session->addSuccess($this->__('Problem voiding track %s: %s', $track->getNumber(), $e->getMessage()));
                        }
                    } catch (Exception $e) {
                        // doesn't support voiding
                    }

                    $track->delete();
                    if ($track->getPackageCount()>1) {
                        foreach (Mage::getResourceModel('sales/order_shipment_track_collection')
                            ->addAttributeToFilter('master_tracking_id', $track->getMasterTrackingId())
                            as $_track
                        ) {
                            $_track->delete();
                        }
                    }
                    Mage::helper('udropship')->addShipmentComment(
                        $shipment,
                        $this->__('%s deleted tracking ID %s', $vendor->getVendorName(), $track->getNumber())
                    );
                    $shipment->getCommentsCollection()->save();
                    #$save = true;
                    $highlight['tracking'] = true;
                    $session->addSuccess($this->__('Track %s was deleted', $track->getNumber()));
                } else {
                    $session->addError($this->__('Track %s was not found', $track->getNumber()));
                }
            }

            $session->setHighlight($highlight);
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_forward('shipmentInfo');
    }

    /**
    * Download one packing slip
    *
    */
    public function pdfAction()
    {
        try {
            $id = $this->getRequest()->getParam('shipment_id');
            if (!$id) {
                Mage::throwException('Invalid shipment ID is supplied');
            }

            $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', $id)
                ->load();
            if (!$shipments->getSize()) {
                Mage::throwException('No shipments found with supplied IDs');
            }

            return $this->_preparePackingSlips($shipments);

        } catch (Exception $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
        }
        $this->_redirect('udropship/vendor/');
    }

    /**
    * Download multiple packing slips
    *
    */
    public function packingSlipsAction()
    {
    	$result = array();
        try {
            $shipments = $this->getVendorShipmentCollection();
            if (!$shipments->getSize()) {
                Mage::throwException('No shipments found for these criteria');
            }

            return $this->_preparePackingSlips($shipments);

        } catch (Exception $e) {
        	if ($this->getRequest()->getParam('use_json_response')) {
        		$result = array(
        			'error'=>true,
        			'message'=>$e->getMessage()
        		);
        	} else {
            	$this->_getSession()->addError($this->__($e->getMessage()));
        	}
        }
    	if ($this->getRequest()->getParam('use_json_response')) {
        	$this->getResponse()->setBody(
        		Mage::helper('core')->jsonEncode($result)
        	);
        } else {
        	$this->_redirect('udropship/vendor/', array('_current'=>true, '_query'=>array('submit_action'=>'')));
        }
    }

    /**
    * Generate and print labels batch
    *
    */
    public function labelBatchAction()
    {
    	$result = array();
        try {
            $shipments = $this->getVendorShipmentCollection();
            if (!$shipments->getSize()) {
                Mage::throwException('No shipments found for these criteria');
            }

            Mage::getModel('udropship/label_batch')
                ->setVendor(Mage::getSingleton('udropship/session')->getVendor())
                ->processShipments($shipments, array(), array('mark_shipped'=>true))
                ->prepareLabelsDownloadResponse();

        } catch (Exception $e) {
        	if ($this->getRequest()->getParam('use_json_response')) {
        		$result = array(
        			'error'=>true,
        			'message'=>$e->getMessage()
        		);
        	} else {
            	$this->_getSession()->addError($this->__($e->getMessage()));
        	}
        }
        if ($this->getRequest()->getParam('use_json_response')) {
        	$this->getResponse()->setBody(
        		Mage::helper('core')->jsonEncode($result)
        	);
        } else {
        	$this->_redirect('udropship/vendor/', array('_current'=>true, '_query'=>array('submit_action'=>'')));
        }
    }

    public function existingLabelBatchAction()
    {
    	$result = array();
        try {
            $shipments = $this->getVendorShipmentCollection();
            if (!$shipments->getSize()) {
                Mage::throwException('No shipments found for these criteria');
            }

            Mage::getModel('udropship/label_batch')
                ->setVendor(Mage::getSingleton('udropship/session')->getVendor())
                ->renderShipments($shipments)
                ->prepareLabelsDownloadResponse();

        } catch (Exception $e) {
        	if ($this->getRequest()->getParam('use_json_response')) {
        		$result = array(
        			'error'=>true,
        			'message'=>$e->getMessage()
        		);
        	} else {
            	$this->_getSession()->addError($this->__($e->getMessage()));
        	}
        }
    	if ($this->getRequest()->getParam('use_json_response')) {
        	$this->getResponse()->setBody(
        		Mage::helper('core')->jsonEncode($result)
        	);
        } else {
        	$this->_redirect('udropship/vendor/', array('_current'=>true, '_query'=>array('submit_action'=>'')));
        }
    }

    public function updateShipmentsStatusAction()
    {
        $hlp = Mage::helper('udropship');
        try {
            $shipments = $this->getVendorShipmentCollection();
            $status = $this->getRequest()->getParam('update_status');

            $statusShipped = Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_SHIPPED;
            $statusDelivered = Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED;

            if (!$shipments->getSize()) {
                Mage::throwException($this->__('No shipments found for these criteria'));
            }
            if (is_null($status) || $status==='') {
                Mage::throwException($this->__('No status selected'));
            }

            $shipmentStatuses = false;
            if (Mage::getStoreConfig('udropship/vendor/is_restrict_shipment_status')) {
                $shipmentStatuses = Mage::getStoreConfig('udropship/vendor/restrict_shipment_status');
                if (!is_array($shipmentStatuses)) {
                    $shipmentStatuses = explode(',', $shipmentStatuses);
                }
            }
            foreach ($shipments as $shipment) {
                if (!$shipmentStatuses || (in_array($shipment->getUdropshipStatus(), $shipmentStatuses) && in_array($status, $shipmentStatuses))) {
                    if ($status==$statusShipped || $status==$statusDelivered) {
                        $tracks = $shipment->getAllTracks();
                        if (count($tracks)) {
                            foreach ($tracks as $track) {
                                $hlp->processTrackStatus($track, true, true);
                            }
                        } else {
                            $hlp->completeShipment($shipment, true, $status==$statusDelivered);
                            $hlp->completeOrderIfShipped($shipment, true);
                            $hlp->completeUdpoIfShipped($shipment, true);
                        }
                    }
                    $shipment->setUdropshipStatus($status)->save();
                }
            }
            $this->_getSession()->addSuccess($this->__('Shipment status has been updated for the selected shipments'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
        }
        $this->_redirect('udropship/vendor/', array('_current'=>true, '_query'=>array('submit_action'=>'')));
    }

    public function reprintLabelBatchAction()
    {
        $hlp = Mage::helper('udropship');

        if (($trackId = $this->getRequest()->getParam('track_id'))) {
            $track = Mage::getModel('sales/order_shipment_track')->load($trackId);
            if (!$track->getId()) {
                return;
            }
            $labelModel = $hlp->getLabelTypeInstance($track->getLabelFormat());
            $labelModel->printTrack($track);
        }

        if (($batchId = $this->getRequest()->getParam('batch_id'))) {
            $batch = Mage::getModel('udropship/label_batch')->load($batchId);
            if (!$batch->getId()) {
                return;
            }
            $labelModel = Mage::helper('udropship')->getLabelTypeInstance($batch->getLabelType());
            $labelModel->printBatch($batch);
        }
    }

    protected function _preparePackingSlips($shipments)
    {
        $vendorId = $this->_getSession()->getId();
        $vendor = Mage::helper('udropship')->getVendor($vendorId);

        foreach ($shipments as $shipment) {
            if ($shipment->getUdropshipVendor()!=$vendorId) {
                Mage::throwException('You are not authorized to print this shipment');
            }
        }

        if (Mage::getStoreConfig('udropship/vendor/ready_on_packingslip')) {
            foreach ($shipments as $shipment) {
                Mage::helper('udropship')->addShipmentComment(
                    $shipment,
                    $this->__('%s printed packing slip', $vendor->getVendorName())
                );
                if ($shipment->getUdropshipStatus()==Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_PENDING) {
                    $shipment->setUdropshipStatus(Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_READY);
                }
                $shipment->save();
            }
        }

        foreach ($shipments as $shipment) {
            $order = $shipment->getOrder();
            $order->setData('__orig_shipping_amount', $order->getShippingAmount());
            $order->setData('__orig_base_shipping_amount', $order->getBaseShippingAmount());
            $order->setShippingAmount($shipment->getShippingAmount());
            $order->setBaseShippingAmount($shipment->getBaseShippingAmount());
        }

        $theme = explode('/', Mage::getStoreConfig('udropship/admin/interface_theme', 0));
        Mage::getDesign()->setArea('adminhtml')
            ->setPackageName(!empty($theme[0]) ? $theme[0] : 'default')
            ->setTheme(!empty($theme[1]) ? $theme[1] : 'default');

        $pdf = Mage::helper('udropship')->getVendorShipmentsPdf($shipments);
        $filename = 'bloomnation_order_'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf';

        foreach ($shipments as $shipment) {
            $order = $shipment->getOrder();
            $order->setShippingAmount($order->getData('__orig_shipping_amount'));
            $order->setBaseShippingAmount($order->getData('__orig_base_shipping_amount'));
        }

        Mage::helper('udropship')->sendDownload($filename, $pdf->render(), 'application/x-pdf');
    }

    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('udropship/vendor_wysiwyg_form_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));

        $this->getResponse()->setBody($content->toHtml());
    }

    public function getVendorShipmentCollection()
    {
        return Mage::helper('udropship')->getVendorShipmentCollection();
    }
    
    public function manageproductsAction() {
        $this->_renderPage(null, 'manageproducts');
    }
    
    public function editproductAction() {
        //
        $id = $this->getRequest()->getParam('id',0);
        $product = Mage::getModel('catalog/product');
        if($id) {
            $product->load($id);
        }

        if($this->getRequest()->getPost()) {
            $postData = $this->getRequest()->getPost();
            
            if(isset($postData['image'])) {
                $imgPath = Mage::getBaseDir('base').$postData['image'];
                // Delete all previous images
                if ($product->getId()){
                    $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
                    $items = $mediaApi->items($product->getId());
                    foreach($items as $item)
                        $mediaApi->remove($product->getId(), $item['file']);
                }                     
                // Reset media gallery
                $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
                // Add image to media gallery
                $product->addImageToMediaGallery ($imgPath, array ('image','small_image','thumbnail'), true, false);
                // Delete original image to reduce the duplicates number
                @unlink($imgPath);
            } else {
                // Save product data
                foreach($postData as $key=>$value) {
                    // Work with multiselect
                    if(is_array($value)) {
                        $value = implode(',',$value);
                    }
                    // Set the data
                    $product->setData($key, $value);
                }
            }
            // Cat IDs
            $cat = Mage::helper('udropship')->getCategoryByVendor($this->_getSession()->getId());
            if($cat) {
                $product->assignAttrCategories($cat->getId());
            }

            // Mark product as invisible if it's 'Other goods'
            if(in_array(55, $product->getCategoryIds())) {
                $product->setVisibility(1);
            } else {
                $product->setVisibility(4);
            }
            
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); // Make sure product default values are updated
            $success = true;
            $data = array();
            $msg = 'Product Edit Succesful';            
            try {
                $product->save();
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
            }
            // If it's AJAX return message
            if($this->getRequest()->isXmlHttpRequest()) {
                // XML HTTP RESPONSE
                if(isset($postData['image'])) {
                    $imginfo = Mage::helper('urls')->getImagePath($product);
                    exit(json_encode(array('path'=> $imginfo['url'],'image'=> $imginfo['path'],
                        'data'=>(string)Mage::helper('catalog/image')
                        ->init($product,
                        'image')->resize(250))));
                } else {
                    exit(json_encode(compact('success','data','msg')));
                }
            }
            // Redirect
            $this->_redirect('udropship/vendor/manageproducts');
        }
        
        // Thing required for standalone page display
        $this->_setTheme();
        $this->loadLayout();
        $this->getLayout()->getBlock('editproduct')->setProduct($product);
        $this->renderLayout();
    }
    public function checknameAction()
    {
        $params = $this->getRequest()->getParams();
        if($this->getRequest()->isAjax()){
            return Mage::helper('udropship')->checkVendorProductName($params);
        }
    }
    public function addproductAction() {
        $product = Mage::getModel('catalog/product');
        $post = $this->getRequest()->getPost();

        if($post) {

            $api = new Mage_Catalog_Model_Product_Api();
            $attribute_api = new Mage_Catalog_Model_Product_Attribute_Set_Api();
            $attribute_sets = $attribute_api->items();
            // Product required params
            $v = $this->_getSession()->getVendor();
            $sku = Mage::helper('udropship')->getRegionCode($v->getRegionId()).'-'.$v->getId().'-'.Mage::helper('urls')->slugify($post['name']);
            $sku = Mage::helper('udropship')->checkSku($sku);
            $productData = array();
            $productData['website_ids'] = array(1); 
            $productData['status'] = 1;
            $productData['name'] = utf8_encode($post['name']);
            $productData['description'] = utf8_encode($post['short_description']);
            $productData['short_description'] = utf8_encode($post['short_description']);
            $productData['weight'] = 10.00;
            $productData['tax_class_id'] =2;
            
            $new_product_id = $api->create('simple', $attribute_sets[0]['set_id'], $sku, $productData);

            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->loadByProduct( $new_product_id );
             
            $stockItem->setData('use_config_manage_stock', 1);
            $stockItem->setData('qty', 10000);
            $stockItem->setData('min_qty', 0);
            $stockItem->setData('use_config_min_qty', 1);
            $stockItem->setData('min_sale_qty', 0);
            $stockItem->setData('use_config_max_sale_qty', 1);
            $stockItem->setData('max_sale_qty', 0);
            $stockItem->setData('use_config_max_sale_qty', 1);
            $stockItem->setData('is_qty_decimal', 0);
            $stockItem->setData('backorders', 0);
            $stockItem->setData('notify_stock_qty', 0);
            $stockItem->setData('is_in_stock', 1);
            $stockItem->setData('tax_class_id', 0);
             
            $stockItem->save();
            
            
            $product = Mage::getModel('catalog/product')->load($new_product_id);
            // Image
            if(!empty($post['imageFile'])) {
                if($tProduct = $this->getRequest()->getParam('temp_product',false)) {
                    // Image
                    $ext = '.'.pathinfo(parse_url($post['imageFile'], PHP_URL_PATH), PATHINFO_EXTENSION);
                    $filename = md5(mt_rand(0,1111111)).$ext; // Create name
                    $imgPath = Mage::getBaseDir().'/media/vendorimageupload/'.$filename;
                    file_put_contents($imgPath, file_get_contents($post['imageFile'])); // Move image from facebook to local server
                    $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
                    $product->addImageToMediaGallery($imgPath, array ('image','small_image','thumbnail'), true, false); // Add to product gallery
                    @unlink($imgPath); // Remove file that was added to magento
                } else {    
                    $imgPath = Mage::getBaseDir().$post['imageFile'];
                    $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
                    $product->addImageToMediaGallery (Mage::getBaseDir().$post['imageFile'], array ('image','small_image','thumbnail'), true, false);
                    @unlink($imgPath);
                }
            }
            // Product data
            $product->setFlowerType(implode(',',$post['flower_type']));
            $product->setStyle(implode(',',$post['style']));
            $product->setSize(implode(',',$post['size']));
            $product->setOccasions(implode(',',$post['occasions']));
            $product->setColor(implode(',',$post['color']));
            $product->setUdropshipVendor($this->_getSession()->getId());
            $product->setPrice(number_format($post['price'], 2, null, ''));
            
            $cat = Mage::helper('udropship')->getCategoryByVendor($this->_getSession()->getId());
            if($cat) {
                $product->assignAttrCategories($cat->getId());
            }

            // Mark product as invisible if it's 'Other goods'
            if(in_array(55, $product->getCategoryIds())) {
                $product->setVisibility(1);
            } else {
                $product->setVisibility(4);
            }
            
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            
            try {
                $product->save();
                if($tProduct = $this->getRequest()->getParam('temp_product',false)) {
                    Mage::getSingleton('core/resource')
                            ->getConnection('core_write')
                            ->query('DELETE FROM udropship_temp_product WHERE id='.$tProduct);
                }
            } catch(Exception $e) {
            
            }
                
            Mage::helper('custom')->updateZipcodeIndex($product->getId(), $this->_getSession()->getVendor()->getLimitZipcode());
            
            // Update vendor qty
            Mage::helper('udropship/catalog')->massUpdateStock(true, true);            
            
            // Add success message
            $this->_getSession()->addSuccess('Product '.$product->getName().' was added');
            
            // Redirect
            $this->_redirect('udropship/vendor/manageproducts');            
        }
        
        // Thing required for standalone page display
        $this->_setTheme();
        $this->loadLayout();
        $this->getLayout()->getBlock('addproduct')->setProduct($product);
        $this->renderLayout();
    }
    
    /**
     * Delete product action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            try {
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
                $product->delete();
                $this->_getSession()->addSuccess($this->__('The product has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        header("Location: ".$_SERVER['HTTP_REFERER']);exit();
    }

    
    public function facebookAction() {
        // If it's ajax POST update facebook data
        if($this->getRequest()->isXmlHttpRequest()) {
            // Init variables
            $success = false;
            $msg = 'No post data found';
            $data = '';
            $fb = new Facebook_Bloomnation();
            $post = $this->getRequest()->getPost();
            if($post) {
                $vendorId = $this->_getSession()->getId();
                
                if(isset($post['dupdate'])) {
                    $facebookPage = addslashes($post['facebook_page']);
                    $facebookId = addslashes($post['facebook_page_id']);
                    
                    // Insert data into database
                    $success = true;
                    $query = "UPDATE udropship_vendor SET
                        facebook_page='$facebookPage',
                        facebook_page_id='$facebookId'
                        WHERE vendor_id=$vendorId";
                    try {
                        Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
                        // Send greeting email
                      $emailTemplate  = Mage::getModel('core/email_template')
                                              ->loadDefault('udropship_vendor_fbopen');
                       //Create an array of variables to assign to template
                       $emailTemplateVariables = array();
                       $emailTemplateVariables['vendorname'] = 'Test';
                       $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                       $emailTemplate->setSenderName('BloomNation team');
                       $emailTemplate->setSenderEmail('no-reply@bloomnation.com');                       
                       if(!$emailTemplate->send(Mage::getSingleton('udropship/session')->getVendor()->getEmail(),'BloomNation team', $emailTemplateVariables)) {
                           $this->_getSession()->addError('Error sending greeting email');
                       }                        
                    } catch (Exception $e) {
                        $success = false;
                        $msg = $e->getMessage();
                    }
                    $msg = 'Your facebook page succesfully updated!';
                    exit(json_encode(compact('success','msg','data')));                    
                }
                
                
                // Try older URL
                preg_match('/\d{7,}/',$post['facebook_page'], $matches);
                if(!empty($matches)) {
                    $facebookPage = $post['facebook_page'];
                    $facebookId = $matches[0];
                    
                    // Insert data into database
                    $query = "UPDATE udropship_vendor SET
                        facebook_page='$facebookPage',
                        facebook_page_id='$facebookId'
                        WHERE vendor_id=$vendorId";
                    $result = Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
                    $success = true;
                    $msg = 'Your facebook page succesfully updated!';
                    exit(json_encode(compact('success','msg','data')));
                }
                
                preg_match('/facebook.com\/(.*)$/',$post['facebook_page'], $matches);
                $facebookPage = $matches[1];
                try {
                    $fbPageInfo = $fb->api('/'.$facebookPage);
                } catch(Exception $e) {
                    $msg = $e->getMessage();
                    exit(json_encode(compact('success','msg','data')));
                }
                if(isset($fbPageInfo['id'])) {
                    $facebookPage = $fbPageInfo['link'];
                    $facebookId = $fbPageInfo['id'];

                    // Insert data into database
                    $query = "UPDATE udropship_vendor SET
                        facebook_page='$facebookPage',
                        facebook_page_id='$facebookId'
                        WHERE vendor_id=$vendorId";
                    $result = Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
                    $success = true;
                    $msg = 'Your facebook page succesfully updated!'; 
                }
            }
            
            exit(json_encode(compact('success','msg','data')));
        }
        $this->_renderPage(null, 'facebook');
    }
    
    public function storefrontAction() {
        $this->_renderPage(null, 'storefront');
    }
    
    public function connectionsAction() {
        $post = $this->getRequest()->getPost();
        if($post) {
            $success = true;
            
            $emails = array();
            foreach($post['sel_customers'] as $c) {
                $emails[] = Mage::helper('udropship/crypt')->decrypt($c);
            }
            $to = '';
            $subject = $post['subject'];
            $message = $post['message'];
            $headers = 'From: no-reply@bloomnation.com' . "\r\n" .
                        'Reply-To: no-reply@bloomnation.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();            
            
            foreach($emails as $e) {
                $to = $e;
                if(!mail($to, $subject, $message, $headers)) {
                    $success = false;
                }
            }
            
            exit(json_encode(compact('success')));
        }
        
        $this->_renderPage(null, 'connections');
    }
    
    public function batchproductAction() {
        // Exit if not ajax
        if(!$this->getRequest()->isXmlHttpRequest()) { exit('This action only support AJAX requests'); }
        
        $post = $this->getRequest()->getPost();
        if($post) {
            // Init variables
            $msg = 'Products were added';
            $success = true;
            $data = '';
            // Batch process to add products
            foreach($post['data'] as $newProduct) {
                try {
                    Mage::helper('udropship/catalog')->addBatchProduct($newProduct);
                } catch(Exception $e) {
                    $success = false;
                    $msg = $e->getMessage();
                    $data = $newProduct;
                    exit(json_encode(compact('success','msg','data')));
                }
            }
            // Send response
            exit(json_encode(compact('success','msg','data')));
        }
        
        $this->_renderPage();
    }
    
    // Temporary product, tempproduct
    public function temprAction() {
        // Exit if not ajax
        //if(!$this->getRequest()->isXmlHttpRequest()) { exit('This action only support AJAX requests'); }
        
        $post = $this->getRequest()->getPost();
        if($post) {
            $success = true;
            $msg = array();
            $id = $this->_getSession()->getId();
            
            foreach($post['img'] as $img) {    
                $query = "INSERT INTO udropship_temp_product
                                VALUES ('', $id, '$img') ";
                try {
                    Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
                } catch(Exception $e) {
                    $success = false;
                    $msg[] = $e->getMessage();
                }
            }
            
            // Send response
            exit(json_encode(compact('success','msg')));
        }
        
        if($delProduct = $this->getRequest()->getParam('del_product',false)) {
            try {
                Mage::getSingleton('core/resource')
                    ->getConnection('core_write')
                    ->query('DELETE FROM udropship_temp_product WHERE id='.$delProduct);
            } catch(Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        
        $this->_renderPage();
    }
}
