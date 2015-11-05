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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * CatalogSearch Fulltext Index resource model
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Fulltext extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Searchable attributes cache
     *
     * @var array
     */
    protected $_searchableAttributes     = null;

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator                = '|';

    /**
     * Array of Zend_Date objects per store
     *
     * @var array
     */
    protected $_dates                    = array();

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes             = array();

    /**
     * Store search engine instance
     *
     * @var object
     */
    protected $_engine                   = null;

    /**
     * Whether table changes are allowed
     *
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
        $this->_engine = Mage::helper('catalogsearch')->getEngine();
    }

    /**
     * Return options separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Update category'es products indexes
     *
     * @param array $productIds
     * @param array $categoryIds
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
            $this->_engine->updateCategoryIndex($productIds, $categoryIds);
        }

        return $this;
    }

    /**
     * Regenerate search index for store(s)
     *
     * @param  int|null $storeId
     * @param  int|array|null $productIds
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        if (is_null($storeId)) {
            $storeIds = array_keys(Mage::app()->getStores());
            foreach ($storeIds as $storeId) {
                $this->_rebuildStoreIndex($storeId, $productIds);
            }
        } else {
            $this->_rebuildStoreIndex($storeId, $productIds);
        }

        return $this;
    }

    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int|array $productIds Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _rebuildStoreIndex($storeId, $productIds = null)
    {
        $this->cleanIndex($storeId, $productIds);

        // prepare searchable attributes
        $staticFields   = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }
        $dynamicFields  = array(
            'int'       => array_keys($this->_getSearchableAttributes('int')),
            'varchar'   => array_keys($this->_getSearchableAttributes('varchar')),
            'text'      => array_keys($this->_getSearchableAttributes('text')),
            'decimal'   => array_keys($this->_getSearchableAttributes('decimal')),
            'datetime'  => array_keys($this->_getSearchableAttributes('datetime')),
        );

        // status and visibility filter
        $visibility     = $this->_getSearchableAttribute('visibility');
        $status         = $this->_getSearchableAttribute('status');
        $visibilityVals = Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds();
        $statusVals     = Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();

        $lastProductId = 0;
        while (true) {
            $products = $this->_getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId);
            if (!$products) {
                break;
            }

            $productAttributes = array();
            $productRelations  = array();
            foreach ($products as $productData) {
                $lastProductId = $productData['entity_id'];
                $productAttributes[$productData['entity_id']] = $productData['entity_id'];
                $productChilds = $this->_getProductChildIds($productData['entity_id'], $productData['type_id']);
                $productRelations[$productData['entity_id']] = $productChilds;
                if ($productChilds) {
                    foreach ($productChilds as $productChildId) {
                        $productAttributes[$productChildId] = $productChildId;
                    }
                }
            }

            $productIndexes    = array();
            $productAttributes = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
            foreach ($products as $productData) {
                /*
                 * If using advanced index and there is no required fields - do not add to index.
                 * Skipping out of stock products if there are no prices for them in catalog_product_index_price table
                 */
                if ($this->_engine->allowAdvancedIndex()
                    && (!isset($productData[$this->_engine->getFieldsPrefix() . 'categories']))
                ) {
                    continue;
                }
                if (!isset($productAttributes[$productData['entity_id']])) {
                    continue;
                }

                $productAttr = $productAttributes[$productData['entity_id']];
                if (!isset($productAttr[$visibility->getId()])
                    || (!in_array($productAttr[$visibility->getId()], $visibilityVals)
                        && !$this->_engine->allowAdvancedIndex()
                    )
                ) {
                    continue;
                }
                if (!isset($productAttr[$status->getId()])
                    || !in_array($productAttr[$status->getId()], $statusVals)
                ) {
                    continue;
                }

                $productIndex = array(
                    $productData['entity_id'] => $productAttr
                );

                if ($productChilds = $productRelations[$productData['entity_id']]) {
                    foreach ($productChilds as $productChildId) {
                        if (isset($productAttributes[$productChildId])) {
                            $productIndex[$productChildId] = $productAttributes[$productChildId];
                        }
                    }
                }

                $index = $this->_prepareProductIndex($productIndex, $productData, $storeId);

                $productIndexes[$productData['entity_id']] = $index;
            }

            $this->_saveProductIndexes($storeId, $productIndexes);
        }

        $this->resetSearchResults();

        return $this;
    }

    /**
     * Retrieve searchable products per store
     *
     * @param int $storeId
     * @param array $staticFields
     * @param array|int $productIds
     * @param int $lastProductId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableProducts($storeId, array $staticFields, $productIds = null, $lastProductId = 0,
        $limit = 100)
    {
        $store  = Mage::app()->getStore($storeId);
        $select = $this->_getWriteAdapter()->select()
            ->useStraightJoin(true)
            ->from(
                array('e' => $this->getTable('catalog/product')),
                array_merge(array('entity_id', 'type_id'), $staticFields)
            )
            ->join(
                array('website' => $this->getTable('catalog/product_website')),
                $this->_getWriteAdapter()->quoteInto(
                    'website.product_id=e.entity_id AND website.website_id=?',
                    $store->getWebsiteId()
                ),
                array()
            )
            ->join(
                array('stock_status' => $this->getTable('cataloginventory/stock_status')),
                $this->_getWriteAdapter()->quoteInto(
                    'stock_status.product_id=e.entity_id AND stock_status.website_id=?',
                    $store->getWebsiteId()
                ),
                array('in_stock' => 'stock_status')
            );

        if (!is_null($productIds)) {
            $select->where('e.entity_id IN(?)', $productIds);
        }

        $select->where('e.entity_id>?', $lastProductId)
            ->limit($limit)
            ->order('e.entity_id');

        $result = $this->_getWriteAdapter()->fetchAll($select);
        if ($this->_engine && $this->_engine->allowAdvancedIndex() && count($result) > 0) {
            return $this->_engine->addAdvancedIndex($result, $storeId, $productIds);
        } else {
            return $result;
        }
    }

    /**
     * Reset search results
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function resetSearchResults()
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->update($this->getTable('catalogsearch/search_query'), array('is_processed' => 0));
        if ($this->_allowTableChanges) {
            $adapter->truncateTable($this->getTable('catalogsearch/result'));
        } else {
            $adapter->delete($this->getTable('catalogsearch/result'));
        }

        Mage::dispatchEvent('catalogsearch_reset_search_result');

        return $this;
    }

    /**
     * Delete search index data for store
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        if ($this->_engine) {
            $this->_engine->cleanIndex($storeId, $productId);
        }
        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        $adapter = $this->_getWriteAdapter();
        if (!$query->getIsProcessed()) {
            $searchType = $object->getSearchType($query->getStoreId());

            $preparedTerms = Mage::getResourceHelper('catalogsearch')
                ->prepareTerms($queryText, $query->getMaxQueryWords());

            $bind = array();
            $like = array();
            $likeCond  = '';
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
            ) {
                $helper = Mage::getResourceHelper('core');
                $words = Mage::helper('core/string')->splitWords($queryText, true, $query->getMaxQueryWords());
                foreach ($words as $word) {
                    $like[] = $helper->getCILike('s.data_index', $word, array('position' => 'any'));
                }
                if ($like) {
                    $likeCond = '(' . join(' OR ', $like) . ')';
                }
            }
            $mainTableAlias = 's';
            $fields = array(
                'query_id' => new Zend_Db_Expr($query->getId()),
                'product_id',
            );
            $select = $adapter->select()
                ->from(array($mainTableAlias => $this->getMainTable()), $fields)
                ->joinInner(array('e' => $this->getTable('catalog/product')),
                    'e.entity_id = s.product_id',
                    array())
                ->where($mainTableAlias.'.store_id = ?', (int)$query->getStoreId());

            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $bind[':query'] = implode(' ', $preparedTerms[0]);
                $where = Mage::getResourceHelper('catalogsearch')
                    ->chooseFulltext($this->getMainTable(), $mainTableAlias, $select);
            }
            if ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                    $where .= ($where ? ' OR ' : '') . $likeCond;
            }
            if ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE) {
                $select->columns(array('relevance'  => new Zend_Db_Expr(0)));
                $where = $likeCond;
            }
            if ($where != '') {
                $select->where($where);
            }

            $sql = $adapter->insertFromSelect($select,
                $this->getTable('catalogsearch/result'),
                array(),
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE);
            $adapter->query($sql, $bind);

            $query->setIsProcessed(1);
        }

        return $this;
    }

    /**
     * Retrieve EAV Config Singleton
     *
     * @return Mage_Eav_Model_Config
     */
    public function getEavConfig()
    {
        return Mage::getSingleton('eav/config');
    }

    /**
     * Retrieve Searchable attributes
     *
     * @param string $backendType
     * @return array
     */
    protected function _getSearchableAttributes($backendType = null)
    {
        if (is_null($this->_searchableAttributes)) {
            $this->_searchableAttributes = array();

            $entityType   = $this->getEavConfig()->getEntityType(Mage_Catalog_Model_Product::ENTITY);
            $entity       = $entityType->getEntity();

            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setEntityTypeFilter($entityType->getEntityTypeId());
            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $productAttributeCollection->addToIndexFilter(true);
            } else {
                $productAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $productAttributeCollection->getItems();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
                $this->_searchableAttributes[$attribute->getId()] = $attribute;
            }
        }
        if (!is_null($backendType)) {
            $attributes = array();
            foreach ($this->_searchableAttributes as $attribute) {
                if ($attribute->getBackendType() == $backendType) {
                    $attributes[$attribute->getId()] = $attribute;
                }
            }

            return $attributes;
        }

        return $this->_searchableAttributes;
    }

    /**
     * Retrieve searchable attribute by Id or code
     *
     * @param int|string $attribute
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function _getSearchableAttribute($attribute)
    {
        $attributes = $this->_getSearchableAttributes();
        if (is_numeric($attribute)) {
            if (isset($attributes[$attribute])) {
                return $attributes[$attribute];
            }
        }
        elseif (is_string($attribute)) {
            foreach ($attributes as $attributeModel) {
                if ($attributeModel->getAttributeCode() == $attribute) {
                    return $attributeModel;
                }
            }
        }
        return $this->getEavConfig()->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
    }

    /**
     * Returns expresion for field unification
     *
     * @param string $field
     * @param string $backendType
     * @return Zend_Db_Expr
     */
    protected function _unifyField($field, $backendType = 'varchar')
    {
        if ($backendType == 'datetime') {
            $expr = Mage::getResourceHelper('catalogsearch')->castField(
                $this->_getReadAdapter()->getDateFormatSql($field, '%Y-%m-%d %H:%i:%s'));
        } else {
            $expr = Mage::getResourceHelper('catalogsearch')->castField($field);
        }
        return $expr;
    }

    /**
     * Load product(s) attributes
     *
     * @param int $storeId
     * @param array $productIds
     * @param array $atributeTypes
     * @return array
     */
    protected function _getProductAttributes($storeId, array $productIds, array $atributeTypes)
    {
        $result  = array();
        $selects = array();
        $adapter = $this->_getReadAdapter();
        $ifStoreValue = $adapter->getCheckSql('t_store.value_id > 0', 't_store.value', 't_default.value');
        foreach ($atributeTypes as $backendType => $attributeIds) {
            if ($attributeIds) {
                $tableName = $this->getTable(array('catalog/product', $backendType));
                $selects[] = $adapter->select()
                    ->from(
                        array('t_default' => $tableName),
                        array('entity_id', 'attribute_id'))
                    ->joinLeft(
                        array('t_store' => $tableName),
                        $adapter->quoteInto(
                            't_default.entity_id=t_store.entity_id' .
                                ' AND t_default.attribute_id=t_store.attribute_id' .
                                ' AND t_store.store_id=?',
                            $storeId),
                        array('value' => $this->_unifyField($ifStoreValue, $backendType)))
                    ->where('t_default.store_id=?', 0)
                    ->where('t_default.attribute_id IN (?)', $attributeIds)
                    ->where('t_default.entity_id IN (?)', $productIds);
            }
        }

        if ($selects) {
            $select = $adapter->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
            $query = $adapter->query($select);
            while ($row = $query->fetch()) {
                $result[$row['entity_id']][$row['attribute_id']] = $row['value'];
            }
        }

        return $result;
    }

    /**
     * Retrieve Product Type Instance
     *
     * @param string $typeId
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    protected function _getProductTypeInstance($typeId)
    {
        if (!isset($this->_productTypes[$typeId])) {
            $productEmulator = $this->_getProductEmulator();
            $productEmulator->setTypeId($typeId);

            $this->_productTypes[$typeId] = Mage::getSingleton('catalog/product_type')
                ->factory($productEmulator);
        }
        return $this->_productTypes[$typeId];
    }

    /**
     * Return all product children ids
     *
     * @param int $productId Product Entity Id
     * @param string $typeId Super Product Link Type
     * @return array
     */
    protected function _getProductChildIds($productId, $typeId)
    {
        $typeInstance = $this->_getProductTypeInstance($typeId);
        $relation = $typeInstance->isComposite()
            ? $typeInstance->getRelationInfo()
            : false;

        if ($relation && $relation->getTable() && $relation->getParentFieldName() && $relation->getChildFieldName()) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('main' => $this->getTable($relation->getTable())),
                    array($relation->getChildFieldName()))
                ->where("{$relation->getParentFieldName()}=?", $productId);
            if (!is_null($relation->getWhere())) {
                $select->where($relation->getWhere());
            }
            return $this->_getReadAdapter()->fetchCol($select);
        }

        return null;
    }

    /**
     * Retrieve Product Emulator (Varien Object)
     *
     * @return Varien_Object
     */
    protected function _getProductEmulator()
    {
        $productEmulator = new Varien_Object();
        $productEmulator->setIdFieldName('entity_id');
        return $productEmulator;
    }

    /**
     * Prepare Fulltext index value for product
     *
     * @param array $indexData
     * @param array $productData
     * @param int $storeId
     * @return string
     */
    protected function _prepareProductIndex($indexData, $productData, $storeId)
    {
        $index = array();

        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (isset($productData[$attributeCode])) {
                $value = $this->_getAttributeValue($attribute->getId(), $productData[$attributeCode], $storeId);
                if ($value) {
                    //For grouped products
                    if (isset($index[$attributeCode])) {
                        if (!is_array($index[$attributeCode])) {
                            $index[$attributeCode] = array($index[$attributeCode]);
                        }
                        $index[$attributeCode][] = $value;
                    }
                    //For other types of products
                    else {
                        $index[$attributeCode] = $value;
                    }
                }
            }
        }

        foreach ($indexData as $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                $value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
                if (!is_null($value) && $value !== false) {
                    $code = $this->_getSearchableAttribute($attributeId)->getAttributeCode();
                    //For grouped products
                    if (isset($index[$code])) {
                        if (!is_array($index[$code])) {
                            $index[$code] = array($index[$code]);
                        }
                        $index[$code][] = $value;
                    }
                    //For other types of products
                    else {
                        $index[$code] = $value;
                    }
                }
            }
        }

        $product = $this->_getProductEmulator()
            ->setId($productData['entity_id'])
            ->setTypeId($productData['type_id'])
            ->setStoreId($storeId);
        $typeInstance = $this->_getProductTypeInstance($productData['type_id']);
        if ($data = $typeInstance->getSearchableData($product)) {
            $index['options'] = $data;
        }

        if (isset($productData['in_stock'])) {
            $index['in_stock'] = $productData['in_stock'];
        }

        if ($this->_engine) {
            if ($this->_engine->allowAdvancedIndex()) {
                $index += $this->_engine->addAllowedAdvancedIndexField($productData);
            }

            return $this->_engine->prepareEntityIndex($index, $this->_separator);
        }

        return Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator);
    }

    /**
     * Retrieve attribute source value for search
     *
     * @param int $attributeId
     * @param mixed $value
     * @param int $storeId
     * @return mixed
     */
    protected function _getAttributeValue($attributeId, $value, $storeId)
    {
        $attribute = $this->_getSearchableAttribute($attributeId);
        if (!($attribute->getIsSearchable() ||
            $attribute->getIsVisibleInAdvancedSearch() ||
            $attribute->getIsFilterable() ||
            $attribute->getIsFilterableInSearch())) {
            return null;
        }

        if ($attribute->usesSource()) {
            $attribute->setStoreId($storeId);
            $value = $attribute->getSource()->getOptionText($value);
        }
        if ($attribute->getBackendType() == 'datetime') {
            $value = $this->_getStoreDate($storeId, $value);
        }

        $inputType = $attribute->getFrontend()->getInputType();
        if ($inputType == 'price') {
            $value = Mage::app()->getStore($storeId)->roundPrice($value);
        }

        if (is_array($value)) {
            $value = implode($this->_separator, $value);
        } elseif (empty($value) && ($inputType == 'select' || $inputType == 'multiselect')) {
            return null;
        }

        return preg_replace("#\s+#siu", ' ', trim(strip_tags($value)));
    }

    /**
     * Save Product index
     *
     * @param int $productId
     * @param int $storeId
     * @param string $index
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _saveProductIndex($productId, $storeId, $index)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndex($productId, $storeId, $index);
        }
        return $this;
    }

    /**
     * Save Multiply Product indexes
     *
     * @param int $storeId
     * @param array $productIndexes
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _saveProductIndexes($storeId, $productIndexes)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndexes($storeId, $productIndexes);
        }
        return $this;
    }

    /**
     * Retrieve Date value for store
     *
     * @param int $storeId
     * @param string $date
     * @return string
     */
    protected function _getStoreDate($storeId, $date = null)
    {
        if (!isset($this->_dates[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dates[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (!is_empty_date($date)) {
            list($dateObj, $format) = $this->_dates[$storeId];
            $dateObj->setDate($date, Varien_Date::DATETIME_INTERNAL_FORMAT);

            return $dateObj->toString($format);
        }

        return null;
    }

    /**
     * Set whether table changes are allowed
     *
     * @param bool $value
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }
}
