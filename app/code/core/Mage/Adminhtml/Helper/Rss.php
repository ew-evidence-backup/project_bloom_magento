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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Rss extends Mage_Core_Helper_Abstract
{
    public function authAdmin($path)
    {
        $session = Mage::getSingleton('rss/session');
        if ($session->isAdminLoggedIn()) {
            return;
        }
        list($username, $password) = Mage::helper('core/http')->authValidate();
        $adminSession = Mage::getModel('admin/session');
        $user = $adminSession->login($username, $password);
        //$user = Mage::getModel('admin/user')->login($username, $password);
        if($user && $user->getId() && $user->getIsActive() == '1' && $adminSession->isAllowed($path)){
            $session->setAdmin($user);
        } else {
            Mage::helper('core/http')->authFailed();
        }
    }
}
