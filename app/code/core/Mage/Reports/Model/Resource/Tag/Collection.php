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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Report Products Tags collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Tag_Collection extends Mage_Tag_Model_Resource_Popular_Collection
{
    /**
     * Add group by tag
     *
     * @deprecated after 1.4.0.1
     *
     * @return Mage_Reports_Model_Resource_Tag_Collection
     */
    public function addGroupByTag()
    {
        return $this;
    }

    /**
     * Add tag popularity to select by specified store ids
     *
     * @param int|array $storeIds
     * @return Mage_Reports_Model_Resource_Tag_Collection
     */
    public function addPopularity($storeIds)
    {
        $select = $this->getSelect()
            ->joinLeft(
                array('tr' => $this->getTable('tag/relation')),
                'main_table.tag_id = tr.tag_id',
                array('popularity' => 'COUNT(tr.tag_id)')
            );
        if (!empty($storeIds)) {
            $select->where('tr.store_id IN(?)', $storeIds);
        }
        $select->group('main_table.tag_id');

        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }
}
