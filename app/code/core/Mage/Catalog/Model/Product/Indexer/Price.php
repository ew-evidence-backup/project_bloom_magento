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
 * Enter description here ...
 *
 * @method Mage_Catalog_Model_Resource_Product_Indexer_Price _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Indexer_Price getResource()
 * @method Mage_Catalog_Model_Product_Indexer_Price setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method Mage_Catalog_Model_Product_Indexer_Price setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method Mage_Catalog_Model_Product_Indexer_Price setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method Mage_Catalog_Model_Product_Indexer_Price setTaxClassId(int $value)
 * @method float getPrice()
 * @method Mage_Catalog_Model_Product_Indexer_Price setPrice(float $value)
 * @method float getFinalPrice()
 * @method Mage_Catalog_Model_Product_Indexer_Price setFinalPrice(float $value)
 * @method float getMinPrice()
 * @method Mage_Catalog_Model_Product_Indexer_Price setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method Mage_Catalog_Model_Product_Indexer_Price setMaxPrice(float $value)
 * @method float getTierPrice()
 * @method Mage_Catalog_Model_Product_Indexer_Price setTierPrice(float $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Indexer_Price extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Matched Entities instruction array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Mage_Core_Model_Config_Data::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Customer_Model_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    protected $_relatedConfigSettings = array(
        Mage_Catalog_Helper_Data::XML_PATH_PRICE_SCOPE,
        Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_indexer_price');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Product Prices');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Index product prices');
    }

    /**
     * Retrieve attribute list has an effect on product price
     *
     * @return array
     */
    protected function _getDependentAttributes()
    {
        return array(
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'tax_class_id',
            'status',
            'required_options',
            'force_reindex_required'
        );
    }

    /**
     * Check if event can be matched by process.
     * Rewrited for checking configuration settings save (like price scope).
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data       = $event->getNewData();
        $resultKey = 'catalog_product_price_match_result';
        if (isset($data[$resultKey])) {
            return $data[$resultKey];
        }

        $result = null;
        if ($event->getEntity() == Mage_Core_Model_Config_Data::ENTITY) {
            $data = $event->getDataObject();
            if (in_array($data->getPath(), $this->_relatedConfigSettings)) {
                $result = $data->isValueChanged();
            } else {
                $result = false;
            }
        } elseif ($event->getEntity() == Mage_Customer_Model_Group::ENTITY) {
            $result = $event->getDataObject()->isObjectNew();
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData($resultKey, $result);

        return $result;
    }

    /**
     * Register data required by catalog product delete process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_price_parent_ids', $parentIds);
        }
    }

    /**
     * Register data required by catalog product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCatalogProductSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product      = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = $product->getIsRelationsChanged() || $product->getIsCustomOptionChanged()
            || $product->dataHasChangedFor('tier_price_changed')
            || $product->getIsChangedWebsites()
            || $product->getForceReindexRequired();

        foreach ($attributes as $attributeCode) {
            $reindexPrice = $reindexPrice || $product->dataHasChangedFor($attributeCode);
        }

        if ($reindexPrice) {
            $event->addNewData('product_type_id', $product->getTypeId());
            $event->addNewData('reindex_price', 1);
        }
    }

    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        /* @var $actionObject Varien_Object */
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexPrice = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexPrice = true;
        }

        // register affected products
        if ($reindexPrice) {
            $event->addNewData('reindex_price_product_ids', $actionObject->getProductIds());
        }
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();

        if ($entity == Mage_Core_Model_Config_Data::ENTITY || $entity == Mage_Customer_Model_Group::ENTITY) {
            $process = $event->getProcess();
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        } else if ($entity == Mage_Catalog_Model_Convert_Adapter_Product::ENTITY) {
            $event->addNewData('catalog_product_price_reindex_all', true);
        } else if ($entity == Mage_Catalog_Model_Product::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_DELETE:
                    $this->_registerCatalogProductDeleteEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerCatalogProductSaveEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                    $this->_registerCatalogProductMassActionEvent($event);
                    break;
            }

            // call product type indexers registerEvent
            $indexers = $this->_getResource()->getTypeIndexers();
            foreach ($indexers as $indexer) {
                $indexer->registerEvent($event);
            }
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_product_price_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_product_price_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
