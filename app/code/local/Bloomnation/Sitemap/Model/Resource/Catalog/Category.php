<?php
class Bloomnation_Sitemap_Model_Resource_Catalog_Category extends Mage_Sitemap_Model_Resource_Catalog_Category
{

    /**
     * Get category collection array
     *
     * @param unknown_type $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $categories = array();

        $store = Mage::app()->getStore($storeId);
        /* @var $store Mage_Core_Model_Store */

        if (!$store) {
            return false;
        }

        $this->_select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getIdFieldName() . '=?', $store->getRootCategoryId());
        $categoryRow = $this->_getWriteAdapter()->fetchRow($this->_select);

        if (!$categoryRow) {
            return false;
        }

        $urConditions = array(
            'e.entity_id=ur.category_id',
            $this->_getWriteAdapter()->quoteInto('ur.store_id=?', $store->getId()),
            'ur.product_id IS NULL',
            $this->_getWriteAdapter()->quoteInto('ur.is_system=?', 1),
        );
        $urxConditions = array(
            'e.entity_id=urx.entity_id',
            $this->_getWriteAdapter()->quoteInto('urx.store_id=?',0),
            'urx.attribute_id = 162',
        );
        $urkConditions = array(
            'e.entity_id=urk.entity_id',
            $this->_getWriteAdapter()->quoteInto('urk.store_id=?',0),
            'urk.attribute_id = 37',
        );
        $this->_select = $this->_getWriteAdapter()->select()
            ->from(array('e' => $this->getMainTable()), array($this->getIdFieldName()))
            ->joinLeft(
            array('ur' => $this->getTable('core/url_rewrite')),
            join(' AND ', $urConditions),
            array('url'=>'request_path')
        )->joinLeft(
            array('urx' => 'catalog_category_entity_varchar'),
            join(' AND ', $urxConditions),
            array('yelp_id'=>'urx.value')
        )->joinLeft(
            array('urk' => 'catalog_category_entity_varchar'),
            join(' AND ', $urkConditions),
            array('url_key'=>'urk.value')
        )
            ->where('e.path LIKE ?', $categoryRow['path'] . '/%');

        $this->_addFilter($storeId, 'is_active', 1);
        echo $this->_select->__toString();
        $query = $this->_getWriteAdapter()->query($this->_select);
        while ($row = $query->fetch()) {
            $category = $this->_prepareCategory($row);
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * Prepare category
     *
     * @param array $categoryRow
     * @return Varien_Object
     */
    protected function _prepareCategory(array $categoryRow)
    {
        $category = new Varien_Object();
        $category->setId($categoryRow[$this->getIdFieldName()]);
        $categoryUrl = $this->getCategoryUrl($categoryRow,$category);
        $category->setUrl($categoryUrl);
        return $category;
    }
    protected function getCategoryUrl($categoryRow,$category)
    {
        if(($vendor = Mage::getModel('udropship/vendor')->load($categoryRow['yelp_id']))) {
            $city = Mage::helper('urls')->slugify($vendor->getCity());
            $url = $city.'-'.$categoryRow['url_key'].'.html';
        }
        if(empty($url))
        {
            $categoryUrl = !empty($categoryRow['url']) ? $categoryRow['url'] : 'catalog/category/view/id/' . $category->getId();
            return $categoryUrl;

        }
        return trim($url);
    }
}