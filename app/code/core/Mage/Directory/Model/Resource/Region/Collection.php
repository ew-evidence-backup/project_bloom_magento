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
 * Country collection
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Resource_Region_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Locale region name table name
     *
     * @var string
     */
    protected $_regionNameTable;

    /**
     * Country table name
     *
     * @var string
     */
    protected $_countryTable;

    /**
     * Define main, country, locale region name tables
     *
     */
    protected function _construct()
    {
        $this->_init('directory/region');

        $this->_countryTable    = $this->getTable('directory/country');
        $this->_regionNameTable = $this->getTable('directory/country_region_name');
    }

    /**
     * Initialize select object
     *
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = Mage::app()->getLocale()->getLocaleCode();

        $this->addBindParam(':region_locale', $locale);
        $this->getSelect()->joinLeft(
            array('rname' => $this->_regionNameTable),
            'main_table.region_id = rname.region_id AND rname.locale = :region_locale',
            array('name'));

        return $this;
    }

    /**
     * Filter by country_id
     *
     * @param string|array $countryId
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function addCountryFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter('main_table.country_id', array('in' => $countryId));
            } else {
                $this->addFieldToFilter('main_table.country_id', $countryId);
            }
        }
        return $this;
    }

    /**
     * Filter by country code (ISO 3)
     *
     * @param string $countryCode
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function addCountryCodeFilter($countryCode)
    {
        $this->getSelect()
            ->joinLeft(
                array('country' => $this->_countryTable),
                'main_table.country_id = country.country_id'
                )
            ->where('country.iso3_code = ?', $countryCode);

        return $this;
    }

    /**
     * Filter by Region code
     *
     * @param string|array $regionCode
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function addRegionCodeFilter($regionCode)
    {
        if (!empty($regionCode)) {
            if (is_array($regionCode)) {
                $this->addFieldToFilter('main_table.code', array('in' => $regionCode));
            } else {
                $this->addFieldToFilter('main_table.code', $regionCode);
            }
        }
        return $this;
    }

    /**
     * Filter by region name
     *
     * @param string|array $regionName
     * @return Mage_Directory_Model_Resource_Region_Collection
     */
    public function addRegionNameFilter($regionName)
    {
        if (!empty($regionName)) {
            if (is_array($regionName)) {
                $this->addFieldToFilter('main_table.default_name', array('in' => $regionName));
            } else {
                $this->addFieldToFilter('main_table.default_name', $regionName);
            }
        }
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_toOptionArray('region_id', 'default_name', array('title' => 'default_name'));
        if (count($options) > 0) {
            array_unshift($options, array(
                'title '=> null,
                'value' => '0',
                'label' => Mage::helper('directory')->__('-- Please select --')
            ));
        }
        return $options;
    }
}
