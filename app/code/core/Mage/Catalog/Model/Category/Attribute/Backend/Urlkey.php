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
 * Category url key attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Attribute_Backend_Urlkey extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Enter description here...
     *
     * @param Varien_Object $object
     * @return Mage_Catalog_Model_Category_Attribute_Backend_Urlkey
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false) {
            return $this;
        }
        if ($urlKey=='') {
            $urlKey = $object->getName();
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $object
     */
    public function afterSave($object)
    {
        /* @var $object Mage_Catalog_Model_Category */
        /**
         * Logic moved to Mage_Catalog_Molde_Indexer_Url
         */
        /*if (!$object->getInitialSetupFlag() && $object->getLevel() > 1) {
            if ($object->dataHasChangedFor('url_key') || $object->getIsChangedProductList()) {
                Mage::getSingleton('catalog/url')->refreshCategoryRewrite($object->getId());
            }
        }*/
    }

}
