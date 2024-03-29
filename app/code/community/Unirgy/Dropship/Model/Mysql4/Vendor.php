<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_Dropship
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

class Unirgy_Dropship_Model_Mysql4_Vendor extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('udropship/vendor', 'vendor_id');
    }

    public function getShippingMethodCode($vendor, $carrierCode, $method)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select()
            ->from(array('vs'=>$this->getTable('vendor_shipping')), array('carrier_code'))
            ->where('vendor_id=?', $vendor->getId());

        $vcCode = $read->fetchOne($select);
        if ($vcCode) {
            $carrierCode = $vcCode;
        }

        $select = $read->select()
            ->from(array('s'=>$this->getTable('shipping')), array())
            ->join(array('sm'=>$this->getTable('shipping_method')), 'sm.shipping_id=s.shipping_id', array('method_code'))
            ->where('sm.carrier_code=?', $carrierCode)
            ->where('s.shipping_code=?', $method)
        ;
        return $read->fetchOne($select);

    }

    public function getShippingMethods($vendor, $system=false)
    {
        $read = $this->_getReadAdapter();
        $prefCarrierSql = new Zend_Db_Expr("'{$vendor->getCarrierCode()}'");
        $estCarrierSql = new Zend_Db_Expr("IFNULL(vs.est_carrier_code,$prefCarrierSql)");
        $carrierSql = new Zend_Db_Expr("IF(vs.carrier_code='**estimate**',$estCarrierSql,IFNULL(vs.carrier_code,$prefCarrierSql))");
        $select = $read->select()
            ->from(array('vs'=>$this->getTable('vendor_shipping')), array())
            ->join(array('sm'=>$this->getTable('shipping_method')), "sm.shipping_id=vs.shipping_id and sm.carrier_code=$carrierSql", array('method_code'))
            ->where('vs.vendor_id=?', $vendor->getId())
            ->columns(array(
                'vendor_shipping_id', 'vendor_id', 'shipping_id', 'account_id', 
                'price_type', 'price', 'priority', 'handling_fee', 'est_carrier_code',
                'ovrd_carrier_code' => 'vs.carrier_code', 'carrier_code'=>$carrierSql,
                'allow_extra_charge','extra_charge_suffix','extra_charge_type','extra_charge'
            ), 'vs')
        ;
        $rows = $read->fetchAll($select);

        $methods = array();
        foreach ($rows as $r) {
            $methods[$r['shipping_id']] = $r;
        }
        return $methods;
    }

    public function getAssociatedProducts($vendor, $productIds=array())
    {
        $products = $this->getVendorTableProducts($vendor, $productIds);
        $products = $products + $this->getVendorAttributeProducts($vendor, $productIds);
        return $products;
    }

    public function getVendorTableProducts($vendor, $productIds=array())
    {
        $products = array();
        $read = $this->_getReadAdapter();
        $select = $read
            ->select()
            ->from($this->getTable('udropship/vendor_product'))
            ->joinLeft('catalog_product_entity_decimal', 'udropship_vendor_product.product_id=catalog_product_entity_decimal.entity_id')
            ->where('catalog_product_entity_decimal.attribute_id=69') // 69 for Price
            ->where('vendor_id=?', $vendor->getId());
        if ($productIds) {
            $select->where('product_id in (?)', $productIds);
        }
        // Default order desc by ID
        $select->order('product_id DESC');
        $rows = $read->fetchAll($select);
        foreach ($rows as $r) {
            $products[$r['product_id']] = array(
                'vendor_product_id' => $r['vendor_product_id'],
                'product_id' => $r['product_id'],
                'price' => $r['value'],
                'vendor_sku'        => $r['vendor_sku'],
                'vendor_cost'       => !is_null($r['vendor_cost']) ? 1*$r['vendor_cost'] : null,
                'stock_qty'         => !is_null($r['stock_qty']) ? 1*$r['stock_qty'] : null,
            );
        }
        return $products;
    }

    public function getVendorAttributeProducts($vendor, $productIds=array())
    {
        $products = array();

        $read = $this->_getReadAdapter();

        $attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'udropship_vendor');
        $table = $attr->getBackend()->getTable();
        $select = $read->select()
            ->from($table, array('entity_id'))
            ->where('attribute_id=?', $attr->getId())
            ->where('value=?', $vendor->getId());
        if ($productIds) {
            $select->where('entity_id in (?)', $productIds);
        }
        if ($products) {
            $select->where('entity_id not in (?)', array_keys($products));
        }
        // Order by ID Desc
        $select->order('entity_id DESC');
        
        $rows = $read->fetchCol($select);

        foreach ($rows as $id) {
            $products[$id] = array();
        }

        return $products;
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);

        if ($object->getPasswordEnc()) {
            $object->setPassword(Mage::helper('core')->decrypt($object->getPasswordEnc()));
            $object->setPasswordEnc('');
        }
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);

        if ($object->getPassword()) {
            $object->setPasswordHash(Mage::helper('core')->getHash($object->getPassword(), 2));
            $object->setPassword('');
            $object->setPasswordEnc('');
        }
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_saveVendorProducts($object);
        $this->_saveVendorShipping($object);
        $this->enableDisableVendorAction($object);
        return parent::_afterSave($object);
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId() == Mage::helper('udropship')->getLocalVendorId()) {
            Mage::throwException(
                Mage::helper('udropship')->__('Cannot delete local vendor. Please change "Configuration / Drop Shipping / Vendor Options / Local Vendor" before')
            );
        }
        $this->resetVendorProducts($object);
        return parent::_beforeDelete($object);
    }

    public function resetVendorProducts($vendor)
    {
        $write = $this->_getWriteAdapter();

        $localVendorId = Mage::helper('udropship')->getLocalVendorId();

        if (!Mage::helper('udropship')->isUdmultiAvailable()
            && $localVendorId != $vendor->getId()
        ) {
            switch (Mage::getStoreConfig('udropship/customer/vendor_delete_action')) {
                case 'assign_local_enabled':
                    $this->_resetVendorProducts($vendor);
                    break;
                case 'assign_local_disable':
                    $this->_enableDisableVendorProducts($vendor, Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
                    $this->_resetVendorProducts($vendor);
                    break;
                case 'delete':
                    if (($assocPids = $this->getVendorAttributeProducts($vendor))) {
                        $assocPids = array_keys($assocPids);
                        foreach ($assocPids as $productId) {
                            Mage::getSingleton('catalog/product')->load($productId)->delete();
                        }
                    }
                    break;
            }
        }

    }

    protected function _resetVendorProducts($vendor, $newVendor=null)
    {
        $write = $this->_getWriteAdapter();

        if ($newVendor == null) {
            $newVendor = Mage::helper('udropship')->getLocalVendorId();
        }

        if (($newVendor = Mage::helper('udropship')->getVendor($newVendor))
            && ($newVendorId = $newVendor->getId())
        ) {
            $attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'udropship_vendor');
            $where = 'attribute_id='.$attr->getId().' and value='.$vendor->getId();
            $write->update($attr->getBackend()->getTable(), array('value'=>$newVendorId), $where);
        }

        return $this;
    }

    protected function _saveVendorProducts($vendor)
    {
        // new category-product relationships
        $products = $vendor->getPostedProducts();
        if (is_null($products)) {
            return $this;
        }

        $attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'udropship_vendor');
        $multi = Mage::getConfig()->getNode('modules/Unirgy_DropshipMulti');
        $isMulti = $multi && $multi->is('active');
        $vId = $vendor->getId();
        $localVendorId = Mage::helper('udropship')->getLocalVendorId();
        $table = $this->getTable('vendor_product');
        $write = $this->_getWriteAdapter();

        // retrieve old data for updated products
        $old = $vendor->getAssociatedProducts(array_keys($products));

        $insert = array();
        $delete = array();
        $delAttr = array();

        foreach ($products as $id=>$a) {
            if (!(int)$id) {
                continue;
            }
            // delete
            if (isset($a['on']) && $a['on']===false) {
                if (!empty($old[$id]['vendor_product_id'])) { // multi
                    $delete[] = $old[$id]['vendor_product_id'];
                } elseif (isset($old[$id])) { // attr
                    $delAttr[] = $id;
                }
                continue;
            }
            // not needed any more
            unset($a['on']);
            // empty value means NULL
            foreach ($a as $k=>$v) {
                if ($v==='') {
                    $a[$k] = null;
                }
            }
            // insert
            if (!isset($old[$id])) {
                if ($isMulti) {
                    $a['vendor_cost'] = isset($a['vendor_cost']) && $a['vendor_cost']!=='' ? "'".addslashes($a['vendor_cost'])."'" : 'null';
                    $a['stock_qty'] = isset($a['stock_qty']) && $a['stock_qty']!=='' ? "'".addslashes($a['stock_qty'])."'" : 'null';
                    $a['vendor_sku'] = isset($a['vendor_sku']) ? "'".addslashes($a['vendor_sku'])."'" : 'null';
                    $a['priority'] = isset($a['priority']) ? "'".addslashes($a['priority'])."'" : '9999';
                    $insert[] = '('.(int)$vId.','.(int)$id.",".$a['vendor_sku'].",".$a['vendor_cost'].",".$a['stock_qty'].",".$a['priority'].")";
                } else {
                    $insert[] = '('.$attr->getEntityTypeId().', '.$attr->getAttributeId().', '.(int)$id.', '.(int)$vId.')';
                }
            }
            // update
            elseif (!empty($old[$id]['vendor_product_id'])) {
                unset($a['vendor_product_id'], $a['vendor_id'], $a['product_id']); //security
                if (!empty($a)) {
                    $write->update($table, $a, 'vendor_product_id='.(int)$old[$id]['vendor_product_id']);
                }
            }
        }
        if ($insert) {
            if ($isMulti) {
                $write->query("replace into {$table} (vendor_id, product_id, vendor_sku, vendor_cost, stock_qty, priority) values ".join(',', $insert));
            } else {
                $write->query("replace into {$attr->getBackendTable()} (entity_type_id, attribute_id, entity_id, value) values ".join(',', $insert));
            }
        }
        if ($delete) {
            $write->delete($table, $write->quoteInto('vendor_product_id in (?)', $delete).$write->quoteInto(' AND vendor_id=?', $vId));
        }
        if ($delAttr) {
            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in'=>$delAttr));
            foreach ($collection as $product) {
                $product->setUdropshipVendor(null)->save(); // for flat catalog, layered and indexing
            }
        }

        Mage::helper('udropship')->saveThisVendorProducts($products, $vendor);
        if ($isMulti) {
            foreach ($products as $_pId => $_p) {
                if (!(int)$_pId) {
                    continue;
                }
                if (!in_array($_pId, $delAttr)) {
                    Mage::helper('udmulti')->getUdmultiStock($_pId, true);
                    Mage::getModel('cataloginventory/stock_item')
                        ->loadByProduct($_pId)
                        ->setData('__dummy',1)
                        ->save();
                }
            }
        }
        
        $vendor->unsetData('associated_products');
        $vendor->unsetData('posted_products');
    }

    public function enableDisableVendorAction($vendor)
    {
        $write = $this->_getWriteAdapter();
        if (!Mage::helper('udropship')->isUdmultiAvailable()
            && $vendor->dataHasChangedFor('status')
        ) {
            switch (Mage::getStoreConfig('udropship/customer/vendor_enable_disable_action')) {
                case 'enable_disable':
                    switch ($vendor->getStatus()) {
                        case Unirgy_Dropship_Model_Source::VENDOR_STATUS_INACTIVE:
                        case Unirgy_Dropship_Model_Source::VENDOR_STATUS_DISABLED:
                            $prodStatus = Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
                            break;
                        case Unirgy_Dropship_Model_Source::VENDOR_STATUS_ACTIVE:
                            $prodStatus = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
                            break;
                    }
                    $this->_enableDisableVendorProducts($vendor, $prodStatus);
                    break;
            }
        }
        return $this;
    }
    protected function _enableDisableVendorProducts($vendor, $prodStatus)
    {
        $write = $this->_getWriteAdapter();
        if (!$vendor instanceof Unirgy_Dropship_Model_Vendor) {
            $vendor = Mage::helper('udropship')->getVendor($vendor);
        }
        if (($assocPids = $this->getVendorAttributeProducts($vendor))) {
            $assocPids = array_keys($assocPids);
            $pStAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
            $pStUpdateSql = sprintf('update %s set value=%s where entity_id in (%s) and attribute_id=%s',
                $pStAttr->getBackendTable(), $write->quote($prodStatus),
                $write->quote($assocPids), $write->quote($pStAttr->getAttributeId())
            );
            $write->query($pStUpdateSql);
            if (Mage::helper('udropship')->hasMageFeature('indexer_1.4')) {
                Mage::getSingleton('index/indexer')->processEntityAction(
                    Mage::getModel('catalog/product_action')->setProductIds($assocPids)->setAttributesData(array('status'=>$prodStatus)),
                    Mage_Catalog_Model_Product::ENTITY, Mage_Index_Model_Event::TYPE_MASS_ACTION
                );
            }
        }
        return $this;
    }

    public function _saveVendorShipping($vendor)
    {
        /**
         * new category-product relationships
         */
        $shipping = $vendor->getPostedShipping();
        if (is_null($shipping)) {
            return $this;
        }
#echo "NEW: "; var_dump($shipping);
        $vId = $vendor->getId();
        $table = $this->getTable('vendor_shipping');
        $write = $this->_getWriteAdapter();

        // retrieve old data for updated products
        $old = $vendor->getAssociatedShippingMethods();
#echo "OLD: "; var_dump($old);
        $insert = array();
        $delete = array();

        foreach ($shipping as $id=>$a) {
            if (!(int)$id) {
                continue;
            }
            // delete
            if (isset($a['on']) && $a['on']===false) {
                if (!empty($old[$id]['vendor_shipping_id'])) {
                    $delete[] = $old[$id]['vendor_shipping_id'];
                }
                continue;
            }
            // not needed any more
            unset($a['on']);
            // empty value means NULL
            foreach ($a as $k=>$v) {
                if ($v==='') {
                    $a[$k] = null;
                }
            }
            foreach (array('extra_charge_suffix','extra_charge_type','extra_charge') as $exField) {
                if (!empty($a[$exField.'_use_default']) && empty($a['allow_extra_charge'])) {
                    $a[$exField] = null;
                }
            }
            // insert
            if (!isset($old[$id])) {
                $a['carrier_code'] = isset($a['carrier_code']) ? "'".addslashes($a['carrier_code'])."'" : 'null';
                $a['est_carrier_code'] = isset($a['est_carrier_code']) ? "'".addslashes($a['est_carrier_code'])."'" : 'null';
                $a['allow_extra_charge'] = isset($a['allow_extra_charge']) ? "'".addslashes($a['allow_extra_charge'])."'" : 'null';
                $a['extra_charge_suffix'] = isset($a['extra_charge_suffix']) ? "'".addslashes($a['extra_charge_suffix'])."'" : 'null';
                $a['extra_charge_type'] = isset($a['extra_charge_type']) ? "'".addslashes($a['extra_charge_type'])."'" : 'null';
                $a['extra_charge'] = isset($a['extra_charge']) ? "'".addslashes($a['extra_charge'])."'" : 'null';
                $insert[] = '('.(int)$vId.','.(int)$id.",".$a['carrier_code'].",".$a['est_carrier_code'].",".$a['allow_extra_charge'].",".$a['extra_charge_suffix'].",".$a['extra_charge_type'].",".$a['extra_charge'].")";
            }
            // update
            elseif (!empty($old[$id]['vendor_shipping_id'])) {
                unset($a['vendor_shipping_id'], $a['vendor_id'], $a['shipping_id']); //security
                $a = Mage::getResourceSingleton('udropship/helper')->myPrepareDataForTable($table, $a);
#echo 'UPDATE: '.$old[$id]['vendor_shipping_id'].': '; var_dump($a); exit;
                if (!empty($a)) {
                    $write->update($table, $a, 'vendor_shipping_id='.(int)$old[$id]['vendor_shipping_id']);
                }
            }
        }
        if ($insert) {
#echo "INSERT: "; var_dump($insert); exit;
            $write->query("replace into {$table} (vendor_id, shipping_id, carrier_code, est_carrier_code, allow_extra_charge, extra_charge_suffix, extra_charge_type, extra_charge) values ".join(',', $insert));
        }
        if ($delete) {
#echo "DELETE: "; var_dump($delete);
            $write->delete($table, $write->quoteInto('vendor_shipping_id in (?)', $delete).$write->quoteInto(' AND vendor_id=?', $vId));
        }
#exit;
        $vendor->unsetData('shipping_methods');
        $vendor->unsetData('shipping_methods_system');
        $vendor->unsetData('posted_shipping');

        return $this;
    }

    public function updateData($object, $fields)
    {
        $condition = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName().'=?', $object->getId());
        $data = array_intersect_key($this->_prepareDataForSave($object), $fields);
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $condition);
        return $this;
    }
}
