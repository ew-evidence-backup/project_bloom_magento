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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * @deprecated after 1.4.1.0
 * @see Mage_ProductAlert_Block_Product_View
 */
class Mage_ProductAlert_Block_Price extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productalert/price.phtml');
    }

    public function isShow()
    {
        if (!Mage::getStoreConfig('catalog/productalert/allow_price')) {
            return false;
        }

        return true;
    }

    public function getUrl($route = '', $params = array())
    {
        return Mage::helper('productalert')->getSaveUrl('price');
    }
}
