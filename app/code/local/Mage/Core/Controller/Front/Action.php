<?php

/**
 * Base front controller
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Varien_Action
{
    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'frontend';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = 'frontend';

    /**
     * Predispatch: shoud set layout area
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function preDispatch()
    {
        $this->getLayout()->setArea($this->_currentArea);        
        
        parent::preDispatch();
        
        // Layout functions
        if($subD = Mage::registry('subdomain')) {
            Mage::getDesign()->setPackageName('default')->setTheme('shopper');
        }
        
        return $this;
    }

    /**
     * Postdispatch: should set last visited url
     *
     * @return Mage_Core_Controller_Front_Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION )) {
            Mage::getSingleton('core/session')->setLastUrl(Mage::getUrl('*/*/*', array('_current'=>true)));
        }
        return $this;
    }

    /**
     * Translate a phrase
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getRealModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    /**
     * Declare headers and content file in responce for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream',
        $contentLength = null
    ) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                if (!$ioAdapter->fileExists($file)) {
                    Mage::throwException(Mage::helper('core')->__('File not found'));
                }
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }
    
    
    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    protected function _getRefererUrl()
    {
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');

        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecode($url);
        }

        return $refererUrl;
    }
    
    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     *
     * @note: check for XSS safety
     */
    protected function _redirectSuccess($defaultUrl)
    {
        $successUrl = $this->getRequest()->getParam(self::PARAM_NAME_SUCCESS_URL);
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }

        $this->getResponse()->setRedirect($successUrl);
        return $this;
    }

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     *
     * @note: check for XSS safety
     */
    protected function _redirectError($defaultUrl)
    {
        $errorUrl = $this->getRequest()->getParam(self::PARAM_NAME_ERROR_URL);
        if (empty($errorUrl)) {
            $errorUrl = $defaultUrl;
        }

        $this->getResponse()->setRedirect($errorUrl);
        return $this;
    }    
}
