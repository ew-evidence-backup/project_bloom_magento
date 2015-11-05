<?php

class Unirgy_DropshipMulti_Model_Observer
{
    public function udropship_stock_item_getIsInStock($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
        $item = $observer->getEvent()->getItem();
        $vars = $observer->getEvent()->getVars();

        if ($item->getUdmultiStock()) {
            $result = false;
            foreach ($item->getUdmultiStock() as $vId=>$vQty) {
                if (is_null($vQty) || $vQty>0) {
                    $result = true;
                    break;
                }
            }
#Mage::log(__METHOD__.': '.$result);
            $vars['result'] = $result;
        }
    }

    public function udropship_stock_item_checkQty($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
        $item = $observer->getEvent()->getItem();
        $qty = $observer->getEvent()->getQty();
        $vars = $observer->getEvent()->getVars();

        if ($item->getUdmultiStock()) {
            $result = false;
            foreach ($item->getUdmultiStock() as $vId=>$vQty) {
                if (is_null($vQty) || $vQty>=$qty) {
                    $result = true;
                    break;
                }
            }
#Mage::log(__METHOD__.': '.$result);
            $vars['result'] = $result;
        }
    }

    public function udropship_stock_item_getQty($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
        $item = $observer->getEvent()->getItem();
        $vars = $observer->getEvent()->getVars();
        #$vars['qty'] = 0;
#Mage::log(__METHOD__);

        if ($item->getUdmultiStock()) {
            $qty = 0;
            foreach ($item->getUdmultiStock() as $vId=>$vQty) {
                $qty = max($qty, is_null($vQty) ? 100000 : $vQty);
            }
#Mage::log(__METHOD__.': '.$qty);
            $vars['qty'] = $qty;
        }
    }

    public function udropship_stock_item_checkQuoteItemQty($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
#        $item = $observer->getEvent()->getItem();
#        $vars = $observer->getEvent()->getVars();
#var_dump($vars['result']);
#        $vars['result'] = false;
#echo __METHOD__; exit;
    }

    public function udropship_shipment_assign_vendor_skus($observer)
    {
    }
    public function udpo_po_assign_vendor_skus($observer)
    {
    }
    public function udropship_po_add_vendor_skus($observer)
    {
        $po = $observer->getEvent()->getPo();

        $sId = $po->getStoreId();
        $vId = $po->getUdropshipVendor();

        if (!Mage::helper('udmulti')->isActive($sId)) {
            return;
        }
        $res = Mage::getSingleton('core/resource');
        $read = $res->getConnection('udropship_read');
        $table = $res->getTableName('udropship_vendor_product');

        $productIds = array();
        $simpleSkus = array();
        foreach ($po->getAllItems() as $item) {
            if (null === $item->getData('vendor_sku') || $item->getFirstAddVendorSkuFlag()) {
                $productIds[spl_object_hash($item)] = $item->getProductId();
            }
            if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
                if (null === $item->getData('vendor_simple_sku') || $item->getFirstAddVendorSimpleSkuFlag()) {
                    $simpleSkus[spl_object_hash($item)] = $item->getOrderItem()->getProductOptionByCode('simple_sku');
                }
            }
        }

        if (!empty($simpleSkus)) {
            $simpleProducts = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('sku', array('in' => array_values($simpleSkus)));
        }

        foreach ($po->getAllItems() as $item) {
            $oItem = $item->getOrderItem();
            if (!empty($productIds[spl_object_hash($item)])) {
                $pId = $item->getProductId();
                $vSku = $read->fetchOne("select vendor_sku from {$table} where product_id='{$pId}' and vendor_id='{$vId}'");
                $item->setVendorSku($vSku);
                if ($oItem->getProductType() == 'bundle' && $oItem->getChildrenItems()) {
                    $skuTypeAttrId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'sku_type')->getId();
                    $skuType = $read->fetchOne(
                        $read->select()->from($res->getTableName('catalog/product').'_int', array('value'))
                            ->where('attribute_id=?', $skuTypeAttrId)
                            ->where('store_id=0 and entity_id=?', $oItem->getProductId())
                    );
                    if (!$skuType) {
                        $_bundleSkus = array(!empty($vSku) ? $vSku
                            : $read->fetchOne(
                                  $read->select()->from($res->getTableName('catalog/product'), array('sku'))
                                      ->where('entity_id=?', $oItem->getProductId())
                              )
                        );
                        foreach ($oItem->getChildrenItems() as $oiChild) {
                            $_cpId = $oiChild->getProductId();
                            $_cvSku = $read->fetchOne("select vendor_sku from {$table} where product_id='{$_cpId}' and vendor_id='{$vId}'");
                            $_bundleSkus[] = !empty($_cvSku) ? $_cvSku : $oiChild->getSku();
                        }
                        $item->setVendorSku(implode('-', $_bundleSkus));
                    }
                }
            }
            if (!empty($simpleSkus[spl_object_hash($item)]) && isset($simpleProducts)
                && ($simpleProduct = $simpleProducts->getItemByColumnValue('sku', $simpleSkus[spl_object_hash($item)]))
            ) {
                $pId = $simpleProduct->getId();
                $vSku = $read->fetchOne("select vendor_sku from {$table} where product_id='{$pId}' and vendor_id='{$vId}'");
                $item->setVendorSimpleSku($vSku);
            }
        }
    }

    public function attachMultivendorData($products, $isActive, $reload=false)
    {
        Mage::helper('udmulti')->attachMultivendorData($products, $isActive, $reload);
        return $this;
    }

    public function catalog_product_load_after($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
#Mage::log(__METHOD__);
        $product = $observer->getEvent()->getProduct();
        if (!$product instanceof Mage_Catalog_Model_Product) {
            return;
        }
        $this->attachMultivendorData(array($product), false);
    }

    public function catalog_product_collection_load_after($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
#Mage::log(__METHOD__);
        $pCollection = $observer->getEvent()->getCollection();
        if ($pCollection->getFlag('skip_udmulti_load')) return;
        $this->attachMultivendorData($pCollection, false);
    }

    public function sales_quote_item_collection_products_after_load($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
#Mage::log(__METHOD__);
        $pCollection = $observer->getEvent()->getProductCollection();
        $this->attachMultivendorData($pCollection, false);
    }

    public function sales_quote_item_set_product($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
#Mage::log(__METHOD__);
        $p = $observer->getEvent()->getProduct();
        $item = $observer->getEvent()->getQuoteItem();
        $vcKey = sprintf('multi_vendor_data/%s/vendor_cost', $item->getUdropshipVendor());
        if (($vc = $p->getData($vcKey)) && $vc>0) {
            $item->setBaseCost($vc);
            if (($parent = $item->getParentItem()) && $parent->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $parent->setBaseCost($vc);
            }
        }
    }

    public function catalog_product_prepare_save($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        Mage::helper('udmulti/protected')->catalog_product_prepare_save($request, $product);

    }

    public function udropship_stock_item_save_before($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }

        $item = $observer->getEvent()->getItem();
        $product = $item->getProductObject();
        $qty = null;
        if ($product && $product->getUpdateUdmultiVendors()) {
            $data = (array)$product->getUpdateUdmultiVendors();
            if ($data) {
                $qty = 0;
                foreach ($data['vendor_stock'] as $vQty) {
                    $qty = max($qty, is_null($vQty) || $vQty==='' ? 10000 : $vQty);
                }
                $isInStock = $qty>0;
            }
        } else {
            $udm = Mage::helper('udmulti')->getUdmultiStock($item->getProductId());
            $item->setUdmultiSet($udm);
            if ($udm) {
                $qty = 0;
                foreach ($udm as $vQty) {
                    $qty = max($qty, is_null($vQty) || $vQty==='' ? 10000 : $vQty);
                }
                $isInStock = $qty>0;
            }
        }
        if (!is_null($qty)) {
            $item->setIsInStock($isInStock);
            $item->setQty($qty);
        }
    }

    public function cataloginventory_stock_item_save_after($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return;
        }
        $item = $observer->getEvent()->getItem();
        $product = $item->getProductObject();

        if (!$product || !$product->getUdmultiStock()) {
            return;
        }
        $data = (array)$product->getUpdateUdmultiVendors();

        Mage::getSingleton('cataloginventory/stock_status')
            ->updateStatus($product->getId());
    }

    public function catalog_product_save_after($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $post = Mage::app()->getRequest()->getPost();

        $data = $product->getUpdateUdmultiVendors();

        $res = Mage::getSingleton('core/resource');
        $write = $res->getConnection('udropship_write');
        $table = $res->getTableName('udropship/vendor_product');        

        if (empty($data['delete']) && empty($data['insert']) && empty($data['update'])) {
            if(isset($post['udmulti_vendors'][1]['vendor_id']) ) {
                $vId = (int)$post['udmulti_vendors'][1]['vendor_id'];
                $prId = (int)Mage::registry('current_product')->getId();
                $v = array('is_crossell'=>(int)$post['udmulti_vendors'][1]['is_crossell']);

                $write->update($table, $v, "vendor_id=$vId AND product_id=$prId");
            }
            return;
        }

        if (!empty($data['delete'])) {
            $write->delete($table, $write->quoteInto('vendor_product_id in (?)', $data['delete']));
        }

        if (!empty($data['insert'])) {
            foreach ($data['insert'] as $v) {
                $v['product_id'] = $product->getId();
                $write->insert($table, $v);
            }
        }

        if (!empty($data['update'])) {
            foreach ($data['update'] as $id=>$v) {
                $write->update($table, $v, 'vendor_product_id='.(int)$id);
            }
        }
    }

    public function sales_order_item_save_before($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return $this;
        }
        $item = $observer->getEvent()->getItem();
        $children = $item->getChildrenItems() ? $item->getChildrenItems() : $item->getChildren();
        if (!$item->getId() && empty($children)) {
            Mage::helper('udmulti')->updateItemStock($item, -$item->getQtyOrdered());
        }
        return $this;
    }

    public function sales_order_item_cancel($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return $this;
        }
        $item = $observer->getEvent()->getItem();
        $children = $item->getChildrenItems() ? $item->getChildrenItems() : $item->getChildren();
        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();
        if ($item->getId() && $item->getProductId() && empty($children) && $qty) {
            Mage::helper('udmulti')->updateItemStock($item, $qty);
        }
        return $this;
    }

    public function sales_creditmemo_item_save_after($observer)
    {
        if (!Mage::helper('udmulti')->isActive()) {
            return $this;
        }
        $item = $observer->getEvent()->getCreditmemoItem();
        if ($item->getId() && $item->getProductId() && $item->getBackToStock()) {
            Mage::helper('udmulti')->updateItemStock($item, $item->getQty());
        }
        return $this;
    }

    public function adminhtml_version($observer)
    {
        Mage::helper('udropship')->addAdminhtmlVersion('Unirgy_DropshipMulti');
    }
}
