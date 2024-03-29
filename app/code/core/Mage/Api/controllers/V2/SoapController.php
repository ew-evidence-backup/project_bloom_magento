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
 * Webservice main controller
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_V2_SoapController extends Mage_Api_Controller_Action
{
    public function indexAction()
    {
        if(Mage::helper('api/data')->isComplianceWSI()){
            $handler_name = 'soap_wsi';
        } else {
            $handler_name = 'soap_v2';
        }

        /* @var $server Mage_Api_Model_Server */
        $this->_getServer()->init($this, $handler_name, $handler_name)
            ->run();
    }
} // Class Mage_Api_IndexController End
