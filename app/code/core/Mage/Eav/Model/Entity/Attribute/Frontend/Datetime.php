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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


class Mage_Eav_Model_Entity_Attribute_Frontend_Datetime extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Retreive attribute value
     *
     * @param $object
     * @return mixed
     */
    public function getValue(Varien_Object $object)
    {
        $data = '';
        $value = parent::getValue($object);
        $format = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
        );

        if ($value) {
            try {
                $data = Mage::getSingleton('core/locale')->date($value, Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (Exception $e) {
                $data = Mage::getSingleton('core/locale')->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}

