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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Zend html block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Template_Zend extends Mage_Core_Block_Template
{

    protected $_view = null;

    /**
     * Class constructor. Base html block
     *
     * @param      none
     * @return     void
     */
    function _construct()
    {
        parent::_construct();
        $this->_view = new Zend_View();
    }

    public function assign($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            foreach ($key as $k=>$v) {
                $this->assign($k, $v);
            }
        } elseif (!is_null($value)) {
            $this->_view->assign($key, $value);
        }
        return $this;
    }

    public function setScriptPath($dir)
    {
        $this->_view->setScriptPath($dir.DS);
    }

    public function fetchView($fileName)
    {
        return $this->_view->render($fileName);
    }

}
