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
 * @package     Mage_Review
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Default review helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    public function getDetail($origDetail){
        return nl2br(Mage::helper('core/string')->truncate($origDetail, 50));
    }

    /**
     * getDetailHtml return short detail info in HTML
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail){
        return nl2br(Mage::helper('core/string')->truncate($this->escapeHtml($origDetail), 50));
    }

    public function getIsGuestAllowToWrite()
    {
        return Mage::getStoreConfigFlag(self::XML_REVIEW_GUETS_ALLOW);
    }
}
