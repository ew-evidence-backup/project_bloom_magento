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
 * Catalog xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog extends Mage_Core_Block_Template
{
    /**
     * Limit for product sorting fields to return
     */
    const PRODUCT_SORT_FIELDS_NUMBER = 3;

    /**
     * Prefix that used in specifing filters on request
     */
    const REQUEST_FILTER_PARAM_REFIX = 'filter_';

    /**
     * Prefix that used in specifing sort order params on request
     */
    const REQUEST_SORT_ORDER_PARAM_REFIX = 'order_';

    /**
     * Retrieve product sort fields as xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductSortFeildsXmlObject()
    {
        $ordersXmlObject    = Mage::getModel('xmlconnect/simplexml_element', '<orders></orders>');
        /* @var $category Mage_Catalog_Model_Category */
        $category           = Mage::getModel('catalog/category');
        $sortOptions        = $category->getAvailableSortByOptions();
        $sortOptions        = array_slice($sortOptions, 0, self::PRODUCT_SORT_FIELDS_NUMBER);
        $defaultSort        = $category->getDefaultSortBy();
        foreach ($sortOptions as $code => $name) {
            $item = $ordersXmlObject->addChild('item');
            if ($code == $defaultSort) {
                $item->addAttribute('isDefault', 1);
            }
            $item->addChild('code', $code);
            $item->addChild('name', $ordersXmlObject->xmlentities(strip_tags($name)));
        }

        return $ordersXmlObject;
    }
}
