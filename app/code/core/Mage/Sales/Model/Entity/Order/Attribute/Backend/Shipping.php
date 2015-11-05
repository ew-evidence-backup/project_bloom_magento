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
 * Order shipping address backend
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    public function beforeSave($object)
    {
        $shippingAddressId = $object->getShippingAddressId();
        if (is_null($shippingAddressId)) {
            $object->unsetShippingAddressId();
        }
    }

    public function afterSave($object)
    {
        $shippingAddressId = false;
        foreach ($object->getAddressesCollection() as $address) {
            if ('shipping' == $address->getAddressType()) {
                $shippingAddressId = $address->getId();
            }
        }
        if ($shippingAddressId) {
            $object->setShippingAddressId($shippingAddressId);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        }
    }

}
