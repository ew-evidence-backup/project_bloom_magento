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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Layered navigation state model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_State extends Varien_Object
{
    /**
     * Add filter item to layer state
     *
     * @param   Mage_Catalog_Model_Layer_Filter_Item $filter
     * @return  Mage_Catalog_Model_Layer_State
     */
    public function addFilter($filter)
    {
        $filters = $this->getFilters();
        $filters[] = $filter;
        $this->setFilters($filters);
        return $this;
    }

    /**
     * Set layer state filter items
     *
     * @param   array $filters
     * @return  Mage_Catalog_Model_Layer_State
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            Mage::throwException(Mage::helper('catalog')->__('The filters must be an array.'));
        }
        $this->setData('filters', $filters);
        return $this;
    }

    /**
     * Get applied to layer filter items
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = $this->getData('filters');
        if (is_null($filters)) {
            $filters = array();
            $this->setData('filters', $filters);
        }
        return $filters;
    }
}
