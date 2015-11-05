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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Downloadable helper
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check is link shareable or not
     *
     * @param Mage_Downloadable_Model_Link | Mage_Downloadable_Model_Link_Purchased_Item $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_YES:
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
