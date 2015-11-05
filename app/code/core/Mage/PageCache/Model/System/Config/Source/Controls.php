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
 * @package     Mage_PageCache
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Page cache system config source model
 *
 * @category   Mage
 * @package    Mage_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_System_Config_Source_Controls
{
    /**
     * Return array of external cache controls for using as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (Mage::helper('pagecache')->getCacheControls() as $code => $type) {
            $options[] = array(
                'value' => $code,
                'label' => $type['label']
            );
        }
        return $options;
    }
}
