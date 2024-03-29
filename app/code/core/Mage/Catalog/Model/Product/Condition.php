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

class Mage_Catalog_Model_Product_Condition extends Varien_Object implements Mage_Catalog_Model_Product_Condition_Interface
{
    public function applyToCollection($collection)
    {
        if ($this->getTable() && $this->getPkFieldName()) {
            $collection->joinTable(
                $this->getTable(),
                $this->getPkFieldName().'=entity_id',
                array('affected_product_id'=>$this->getPkFieldName())
            );
        }
        return $this;
    }

    public function getIdsSelect($dbAdapter)
    {
        if ($this->getTable() && $this->getPkFieldName()) {
            $select = $dbAdapter->select()
                ->from($this->getTable(), $this->getPkFieldName());
            return $select;
        }
        return '';
    }
}
