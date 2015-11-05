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
 * @package     Mage_Install
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Download Magento core modules and updates choice (online, offline)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Download extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/download.phtml');
    }

    /**
     * Retrieve locale data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/downloadPost');
    }

    public function getNextUrl()
    {
        return Mage::getModel('install/wizard')
            ->getStepByName('download')
                ->getNextUrl();
    }

    public function hasLocalCopy()
    {
        $dir = Mage::getConfig()->getModuleDir('etc', 'Mage_Adminhtml');
        if ($dir && file_exists($dir)) {
            return true;
        }
        return false;
    }
}

