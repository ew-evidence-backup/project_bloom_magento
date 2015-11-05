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
 * Filters xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Filters extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Render filters list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $categoryId         = $this->getRequest()->getParam('category_id', null);
        $categoryXmlObj     = Mage::getModel('xmlconnect/simplexml_element', '<category></category>');
        $filtersCollection  = Mage::getResourceModel('xmlconnect/filter_collection')->setCategoryId($categoryId);

        $filtersXmlObj = $categoryXmlObj->addChild('filters');
        foreach ($filtersCollection->getItems() as $item) {
            if (!sizeof($item->getValues())) {
                continue;
            }
            $itemXmlObj = $filtersXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $categoryXmlObj->xmlentities($item->getName()));
            $itemXmlObj->addChild('code', $categoryXmlObj->xmlentities($item->getCode()));

            $valuesXmlObj = $itemXmlObj->addChild('values');
            foreach ($item->getValues() as $value) {
                $valueXmlObj = $valuesXmlObj->addChild('value');
                $valueXmlObj->addChild('id', $categoryXmlObj->xmlentities($value->getValueString()));
                $valueXmlObj->addChild('label', $categoryXmlObj->xmlentities(strip_tags($value->getLabel())));
                $valueXmlObj->addChild('count', (int)$value->getProductsCount());
            }
        }
        $categoryXmlObj->appendChild($this->getProductSortFeildsXmlObject());

        return $categoryXmlObj->asNiceXml();
    }
}
