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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Customer default shipping address backend
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer_Attribute_Backend_Shipping extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function beforeSave($object)
    {
        $defaultShipping = $object->getDefaultShipping();
        if (is_null($defaultShipping)) {
            $object->unsetDefaultShipping();
        }
    }
    
    public function afterSave($object)
    {
        if ($defaultShipping = $object->getDefaultShipping()) 
        {
            $addressId = false;
            /**
             * post_index set in customer save action for address
             * this is $_POST array index for address
             */
            foreach ($object->getAddresses() as $address) {
                if ($address->getPostIndex() == $defaultShipping) {
                    $addressId = $address->getId();
                }
            }
            
            if ($addressId) {
                $object->setDefaultShipping($addressId);
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getAttributeCode());
            }
        }
    }
}
