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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Quote entity resource model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Entity_Quote_Address extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('quote_address')->setConnection(
            $resource->getConnection('sales_read'),
            $resource->getConnection('sales_write')
        );
    }

    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $attributes = $this->loadAllAttributes()->getAttributesByCode();
        foreach ($attributes as $attrCode=>$attr) {
            $backend = $attr->getBackend();
            if (is_callable(array($backend, 'collectTotals'))) {
                $backend->collectTotals($address);
            }
        }
        return $this;
    }

    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $attributes = $this->loadAllAttributes()->getAttributesByCode();
        foreach ($attributes as $attrCode=>$attr) {
            $frontend = $attr->getFrontend();
            if (is_callable(array($frontend, 'fetchTotals'))) {
                $frontend->fetchTotals($address);
            }
        }

        return $this;
    }
}
