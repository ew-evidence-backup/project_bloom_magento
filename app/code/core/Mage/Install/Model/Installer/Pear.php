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

require_once "Varien/Pear/Package.php";

/**
 * PEAR Packages Download Manager
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Pear extends Mage_Install_Model_Installer_Abstract
{
    public function getPackages()
    {
        $packages = array(
            'pear/PEAR-stable',
            'connect.magentocommerce.com/core/Mage_Pear_Helpers',
            'connect.magentocommerce.com/core/Lib_ZF',
            'connect.magentocommerce.com/core/Lib_Varien',
            'connect.magentocommerce.com/core/Mage_All',
            'connect.magentocommerce.com/core/Interface_Frontend_Default',
            'connect.magentocommerce.com/core/Interface_Adminhtml_Default',
            'connect.magentocommerce.com/core/Interface_Install_Default',
        );
        return $packages;
    }

    public function checkDownloads()
    {
        $pear = new Varien_Pear;
        $pkg = new PEAR_PackageFile($pear->getConfig(), false);
        $result = true;
        foreach ($this->getPackages() as $package) {
            $obj = $pkg->fromAnyFile($package, PEAR_VALIDATE_NORMAL);
            if (PEAR::isError($obj)) {
                $uinfo = $obj->getUserInfo();
                if (is_array($uinfo)) {
                    foreach ($uinfo as $message) {
                        if (is_array($message)) {
                            $message = $message['message'];
                        }
                        Mage::getSingleton('install/session')->addError($message);
                    }
                } else {
                    print_r($obj->getUserInfo());
                    #Mage::getSingleton('install/session')->addError($message);
                }
                $result = false;
            }
        }
        return $result;
    }
}
