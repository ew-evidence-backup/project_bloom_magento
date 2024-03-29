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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Filter collection
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Filter_Collection extends Varien_Data_Collection
{
    /**
     * Set CategoryId filter
     *
     * @param int $categoryId
     * @return Mage_XmlConnect_Model_Resource_Filter_Collection
     */
    public function setCategoryId($categoryId)
    {
        if ((int)$categoryId > 0) {
            $this->addFilter('category_id', $categoryId);
        }
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_XmlConnect_Model_Resource_Filter_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items)) {
            $layer = Mage::getSingleton('catalog/layer');
            foreach ($this->_filters as $filter) {
                if ('category_id' == $filter['field']) {
                    $layer->setCurrentCategory((int)$filter['value']);
                }
            }
            if ($layer->getCurrentCategory()->getIsAnchor()) {
                foreach ($layer->getFilterableAttributes() as $attributeItem) {
                    $filterModelName = 'catalog/layer_filter_attribute';
                    switch ($attributeItem->getAttributeCode()) {
                        case 'price':
                            $filterModelName = 'catalog/layer_filter_price';
                            break;
                        case 'decimal':
                            $filterModelName = 'catalog/layer_filter_decimal';
                            break;
                        default:
                            $filterModelName = 'catalog/layer_filter_attribute';
                            break;
                    }

                    $filterModel = Mage::getModel($filterModelName);
                    $filterModel->setLayer($layer)->setAttributeModel($attributeItem);
                    $filterValues = new Varien_Data_Collection;
                    foreach ($filterModel->getItems() as $valueItem) {
                        $valueObject = new Varien_Object();
                        $valueObject->setLabel($valueItem->getLabel());
                        $valueObject->setValueString($valueItem->getValueString());
                        $valueObject->setProductsCount($valueItem->getCount());
                        $filterValues->addItem($valueObject);
                    }
                    $item = new Varien_Object;
                    $item->setCode($attributeItem->getAttributeCode());
                    $item->setName($filterModel->getName());
                    $item->setValues($filterValues);
                    $this->addItem($item);
                }
            }
        }
        return $this;
    }
}
