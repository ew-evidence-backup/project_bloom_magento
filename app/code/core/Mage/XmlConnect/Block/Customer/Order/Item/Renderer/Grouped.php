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
 * Customer order item xml renderer for grouped product type
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Grouped extends Mage_Sales_Block_Order_Item_Renderer_Grouped
{
    /**
     * Default product type
     */
    const DEFAULT_PRODUCT_TYPE = 'default';

    /**
     * Add item to XML object
     * (get from template: sales/order/items/renderer/default.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        if (!($item = $this->getItem()->getOrderItem())) {
            $item = $this->getItem();
        }
        if (!($productType = $item->getRealProductType())) {
            $productType = self::DEFAULT_PRODUCT_TYPE;
        }
        $renderer = $this->getRenderedBlock()->getItemRenderer($productType);
        $renderer->setItem($this->getItem());
        $renderer->addItemToXmlObject($orderItemXmlObj);
    }
}
