<?php
class Unirgy_Dropship_FileController extends Mage_Core_Controller_Front_Action
{
    public function uploadAction()
    {

        $allowedExtensions = array('jpeg', 'jpg', 'png', 'gif');
        $sizeLimit = 30 * 1024 * 1024;
        $uploader = new Ajax_Uploader($allowedExtensions, $sizeLimit);
        $dirToUpload = Mage::getBaseDir('media').'/vendorimageupload/';
        $session = Mage::getSingleton('udropship/session');
        $vendor = $session->getVendor();
        $vendorId = $vendor->getId();
        $storeOwner = false;
        if (isset($_GET['store_owner'])) {
            $storeOwner = (int)$_GET['store_owner'];
        }
        if ($vendorId) {
            $dir = Mage::getBaseDir('media').'/vendor/' . $vendorId . '/';
            $dirToUpload = $dir;
            if ($storeOwner) {
                $dir = $dir . 'profile/';
                $dirToUpload = $dir;
            }
        }
        @mkdir($dirToUpload);

        $result = $uploader->handleUpload($dirToUpload,$vendorId);
        $result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        echo $result;
    }
    public function aviaryAction()
    {
        $_product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('pid'));
        $imgInfo = Mage::helper('urls')->getImagePath($_product);
        $path = $imgInfo['path'];
        $pathInfo = pathinfo($path);
        if($image_data = file_get_contents($_REQUEST['url'])) {
            $size = file_put_contents($path, $image_data);
            //Mage::log('Write: '. $size .' To file :'. $path .' Orig source: '. $_REQUEST['url'],0,'param.txt');
                if($pathInfo['basename']) {
                        //Mage::log($pathInfo,0,'param.txt');
                        $output = array();
                        $caches = exec("find ./media/catalog/product/cache -name '".$pathInfo['basename']."'", $output);
                        foreach($output as $cacheI) {
                                //Mage::log($cacheI, 0, 'param.txt');
                                unlink($cacheI);
                        }
                }
        }

    }
}