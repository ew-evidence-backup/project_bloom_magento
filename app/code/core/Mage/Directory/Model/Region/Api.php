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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Directory Region Api
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Region_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve regions list
     *
     * @param string $country
     * @return array
     */
    public function items($country)
    {
        try {
            $country = Mage::getModel('directory/country')->loadByCode($country);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('country_not_exists', $e->getMessage());
        }

        if (!$country->getId()) {
            $this->_fault('country_not_exists');
        }

        $result = array();
        foreach ($country->getRegions() as $region) {
            $region->getName();
            $result[] = $region->toArray(array('region_id', 'code', 'name'));
        }

        return $result;
    }
} // Class Mage_Directory_Model_Region_Api End
