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
 * @package     Mage_Api
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Wsdl base config
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Wsdl_Config_Base extends Varien_Simplexml_Config
{
    protected $_handler = '';

    /**
     * @var Varien_Object
     */
    protected $_wsdlVariables = null;

    protected $_loadedFiles = array();

    public function __construct($sourceData=null)
    {
        $this->_elementClass = 'Mage_Api_Model_Wsdl_Config_Element';
        parent::__construct($sourceData);
    }

    /**
     * Set handler
     *
     * @param string $handler
     * @return Mage_Api_Model_Wsdl_Config_Base
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }

    /**
     * Get handler
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * Processing file data
     *
     * @param string $text
     * @return string
     */
    public function processFileData($text)
    {
        $template = Mage::getModel('core/email_template_filter');

        if (null === $this->_wsdlVariables) {
            $this->_wsdlVariables = new Varien_Object();
            $this->_wsdlVariables->setUrl(Mage::getUrl('*/*/*'));
            $this->_wsdlVariables->setName('Magento');
            $this->_wsdlVariables->setHandler($this->getHandler());
        }

        $template->setVariables(array('wsdl'=>$this->_wsdlVariables));

        $text = $template->filter($text);

        return $text;
    }

    public function addLoadedFile($file)
    {
        if (!in_array($file, $this->_loadedFiles)) {
            $this->_loadedFiles[] = $file;
        }
        return $this;
    }

    public function loadFile($file)
    {
        if (in_array($file, $this->_loadedFiles)) {
            return false;
        }
        $res = parent::loadFile($file);
        if ($res) {
            $this->addLoadedFile($file);
        }
        return $this;
    }
}
