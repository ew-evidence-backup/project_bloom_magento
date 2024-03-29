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
 * @package     Mage_Page
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Page layout config model
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Model_Config
{
    const XML_PATH_PAGE_LAYOUTS = 'global/page/layouts';
    const XML_PATH_CMS_LAYOUTS = 'global/cms/layouts';

    /**
     * Available page layouts
     *
     * @var array
     */
    protected $_pageLayouts = null;

    /**
     * Initialize page layouts list
     *
     * @return Mage_Page_Model_Config
     */
    protected function _initPageLayouts()
    {
        if ($this->_pageLayouts === null) {
            $this->_pageLayouts = array();
            $this->_appendPageLayouts(self::XML_PATH_CMS_LAYOUTS);
            $this->_appendPageLayouts(self::XML_PATH_PAGE_LAYOUTS);
        }
        return $this;
    }

    /**
     * Fill in $_pageLayouts by reading layouts from config
     *
     * @param string $xmlPath XML path to layouts root
     * @return Mage_Page_Model_Config
     */
    protected function _appendPageLayouts($xmlPath)
    {
        if (!Mage::getConfig()->getNode($xmlPath)) {
            return $this;
        }
        if (!is_array($this->_pageLayouts)) {
            $this->_pageLayouts = array();
        }
        foreach (Mage::getConfig()->getNode($xmlPath)->children() as $layoutCode => $layoutConfig) {
            $this->_pageLayouts[$layoutCode] = new Varien_Object(array(
                'label'         => Mage::helper('page')->__((string)$layoutConfig->label),
                'code'          => $layoutCode,
                'template'      => (string)$layoutConfig->template,
                'layout_handle' => (string)$layoutConfig->layout_handle,
                'is_default'    => (int)$layoutConfig->is_default,
            ));
        }
        return $this;
    }

    /**
     * Retrieve available page layouts
     *
     * @return array
     */
    public function getPageLayouts()
    {
        $this->_initPageLayouts();
        return $this->_pageLayouts;
    }

    /**
     * Retrieve page layout by code
     *
     * @param string $layoutCode
     * @return Varien_Object|boolean
     */
    public function getPageLayout($layoutCode)
    {
        $this->_initPageLayouts();

        if (isset($this->_pageLayouts[$layoutCode])) {
            return $this->_pageLayouts[$layoutCode];
        }

        return false;
    }

    /**
     * Retrieve page layout handles
     *
     * @return array
     */
    public function getPageLayoutHandles()
    {
        $handles = array();

        foreach ($this->getPageLayouts() as $layout) {
            $handles[$layout->getCode()] = $layout->getLayoutHandle();
        }

        return $handles;
    }
}
