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
 * @package    Unirgy_DropshipSplit
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

class Unirgy_DropshipMulti_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_multiVendorData = array();

    public function isActive($store=null)
    {
        $method = Mage::getStoreConfig('udropship/stock/availability', $store);
        $config = Mage::getConfig()->getNode("global/udropship/availability_methods/$method");
        return $config && $config->is('multi');
    }

    public function clearMultiVendorData()
    {
        $this->_multiVendorData = array();
        return $this;
    }

    public function getVendorSku($pId, $vId, $defaultSku=null)
    {
        $collection = Mage::getModel('udropship/vendor_product')->getCollection()
            ->addProductFilter($pId)
            ->addVendorFilter($vId);
        foreach ($collection as $item) {
            return $item->getVendorSku() ? $item->getVendorSku() : $defaultSku;
        }
        return $defaultSku;
    }

    public function getMultiVendorData($items, $joinVendors=false, $force=false)
    {
        $key = $joinVendors ? 'vendors,' : 'novendors,';
        $productIds = array();
        foreach ($items as $item) {
            if ($item instanceof Varien_Object) {
                $pId = $item->hasProductId() ? $item->getProductId() : $item->getEntityId();
                $key .= $pId.':'.$item->getQty().',';
                $productIds[] = $pId;
            } elseif (is_scalar($item)) {
                $key .= $item;
                $productIds[] = $item;
            }
        }
        if (empty($this->_multiVendorData[$key]) || $force) {
            $collection = Mage::getModel('udropship/vendor_product')->getCollection()
                ->addProductFilter($productIds);
            if ($joinVendors) {
                $res = Mage::getSingleton('core/resource');
                $collection->getSelect()->join(array('v'=>$res->getTableName('udropship_vendor')), 'v.vendor_id=main_table.vendor_id');
            }
            $this->_multiVendorData[$key] = $collection;
        }
        return $this->_multiVendorData[$key];
    }

    public function getUdmultiStock($productId, $force=false)
    {
        $vCollection = $this->getMultiVendorData(array($productId), false, $force);
        $udmArr = array();
        $qty = 0;
        foreach ($vCollection as $vp) {
            $udmArr[$vp->getVendorId()] = $vp->getStockQty();
        }
        return $udmArr;
    }

    /**
    * Add or subtract qty from vendor-product stock
    *
    * @param mixed $item
    * @param float $qty use negative to subtract stock
    */
    public function updateItemStock($item, $qty, $transaction=null)
    {
        $pId = $item->getProductId();
        $vId = $item->getUdropshipVendor();
        if (!$vId && $item->getOrderItem()) {
            $vId = $item->getOrderItem()->getUdropshipVendor();
        }

        if (!$pId || !$vId) {
            // should never happen
            return;
            Mage::throwException($this->__('Invalid data: vendor_id=%s, product_id=%s', $vId, $pId));
        }

        $v = Mage::helper('udropship')->getVendor($vId);
        if ($v->getStockcheckMethod()) {
            return; // custom stock notification used
        }

        $collection = Mage::getModel('udropship/vendor_product')->getCollection()
            ->addVendorFilter($vId)
            ->addProductFilter($pId);

        if ($collection->count()!==1) {
            // for now silent fail, if the vendor-product association was deleted after order
            return;
            Mage::throwException($this->__('Failed to update vendor stock: vendor is not associated with this item (%s)', $item->getSku()));
        }

        foreach ($collection as $vp) {
            if (is_null($vp->getStockQty())) {
                continue;
            }
            $vp->setStockQty(max($vp->getStockQty()+$qty, 0));
            if ($transaction) {
                $transaction->addObject($vp);
            } else {
                $vp->save();
            }
            if ($item->getProduct()) {
                $product = $item->getProduct();
            } elseif ($item->getOrderItem() && $item->getOrderItem()->getProduct()) {
                $product = $item->getOrderItem()->getProduct();
            }
            if (!empty($product) && ($stockItem = $product->getStockItem())) {
                $stockQty = max($stockItem->getQty()+$qty, 0);
                $stockItem->setQty($stockQty)->setIsInStock($stockQty>0);
                if ($transaction) {
                    $transaction->addObject($stockItem);
                } else {
                    $stockItem->save();
                }
            }
        }

        return $this;
    }

    public function updateOrderItemsVendors($orderId, $vendors)
    {
        // load order
        $order = Mage::getModel('sales/order')->load($orderId);

        // load order items
        $items = $order->getAllItems();

        $isUdpo = Mage::helper('udropship')->isModuleActive('udpo');
        // retrieve all order shipments
        if (!$isUdpo) {
            if (!Mage::helper('udropship')->isSalesFlat()) {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->addAttributeToSelect(array('udropship_vendor', 'total_qty'))
                    ->setOrderFilter($order);
            } else {
                $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($order);
            }
            $shipmentsByVendor = array();
            foreach ($shipments as $s) {
                $s->setOrder($order);
                $shipmentsByVendor[$s->getUdropshipVendor()][] = $s;
            }
        }

        // start save and delete transaction
        $save = Mage::getModel('core/resource_transaction');
        $delete = Mage::getModel('core/resource_transaction');

        $changed = false;
        $vendorIds = array();
        // iterate order items
        foreach ($items as $item) {
            // if no vendor update for the item, continue
            if (empty($vendors[$item->getId()])) {
                continue;
            }
            // get new vendor info
            $v = $vendors[$item->getId()];
            $vId = $v['id'];
            // if vendor didn't change, continue
            if ($vId==$item->getUdropshipVendor()) {
                continue;
            }
            $changed = true;
            // if shipment for the item was generated, collect item and vendor ids
            if (!$isUdpo && !empty($shipmentsByVendor[$item->getUdropshipVendor()])) {
                $vendorIds[$item->getId()] = $vId;
            }
            // calculate item qty to update stock with
            $qty = Mage::helper('udropship')->getItemStockCheckQty($item);
            // update stock for old vendor shipment
            if ($qty) {
                $this->updateItemStock($item, $qty, $save);
            }
            // update order item with new vendor and cost
            $item->setUdropshipVendor($vId);
            $item->setUdmOrigBaseCost($item->getBaseCost());
            if (!is_null($v['cost'])) {
                $item->setBaseCost($v['cost']);
            }
            // update stock for new vendor shipment
            if ($qty) {
                $this->updateItemStock($item, -$qty, $save);
            }
            // add item to save transaction
            $save->addObject($item);

            if ($item->getHasChildren()) {
                $children = $item->getChildrenItems() ? $item->getChildrenItems() : $item->getChildren();
                foreach ($children as $child) {
                    // calculate item qty to update stock with
                    $qty = Mage::helper('udropship')->getItemStockCheckQty($child);
                    // update stock for old vendor shipment
                    if ($qty) {
                        $this->updateItemStock($child, $qty, $save);
                    }
                    // update order item with new vendor and cost
                    $child->setUdropshipVendor($vId);
                    $child->setUdmOrigBaseCost($child->getBaseCost());
                    if (!is_null($v['cost'])) {
                        $child->setBaseCost($v['cost']);
                    }
                    // update stock for new vendor shipment
                    if ($qty) {
                        $this->updateItemStock($child, -$qty, $save);
                    }
                    // add item to save transaction
                    $save->addObject($child);
                }
            }
        }

        $shippedItemIds = array();
        if (!$isUdpo) {
            // in case we'll need to generate new shipments
            $convertor = Mage::getModel('sales/convert_order');

            // clone shipments to avoid affecting the loop by adding a new shipment
            $oldShipments = clone $shipments;
            // iterate shipment items
            foreach ($oldShipments as $oldShipment) {
                $sItems = $oldShipment->getAllItems();
                foreach ($sItems as $sItem) {
                    $orderItemId = $sItem->getOrderItemId();
                    // no changes needed for this order item
                    if (empty($vendorIds[$orderItemId])) {
                        continue;
                    }
                    // get new vendor id
                    $vId = $vendorIds[$orderItemId];
                    $vendor = Mage::helper('udropship')->getVendor($vId);
                    // safeguard against changing vendor twice
                    if ($vId==$oldShipment->getUdropshipVendor()) {
                        continue;
                    }
                    // update old shipment
                    $udmOrigBaseCost = $sItem->getOrderItem()->getUdmOrigBaseCost();
                    $baseCost = $sItem->getOrderItem()->getBaseCost();
                    $oldShipment->setTotalCost($oldShipment->getTotalCost()-$sItem->getQty()*$udmOrigBaseCost);
                    $oldShipment->setTotalQty($oldShipment->getTotalQty()-$sItem->getQty());
                    $oldShipment->getItemsCollection()->removeItemByKey($sItem->getId());
                    // if target shipment already exists, use it
                    if (!empty($shipmentsByVendor[$vId])) {
                        $newShipment = current($shipmentsByVendor[$vId]);
                    } else {
                        // otherwise create a new one
                        $shipmentStatus = Mage::getStoreConfig('udropship/vendor/default_shipment_status', $order->getStoreId());
                        if ('999' != $vendor->getData('initial_shipment_status')) {
                            $shipmentStatus = $vendor->getData('initial_shipment_status');
                        }
                        $newShipment = $convertor->toShipment($order)
                            ->setUdropshipVendor($vId)
                            ->setUdropshipStatus($shipmentStatus);
                        // and add it to collection
                        $shipments->addItem($newShipment);
                        $shipmentsByVendor[$vId][] = $newShipment;
                    }
                    // update the new shipment
                    $newShipment->setTotalCost($newShipment->getTotalCost()+$sItem->getQty()*$baseCost);
                    $newShipment->setTotalQty($newShipment->getTotalQty()+$sItem->getQty());
                    $newShipment->setUdropshipMethod($vendors[$orderItemId]['method']);
                    $newShipment->setUdropshipMethodDescription($vendors[$orderItemId]['method_name']);
                    // retrieve shipment items before adding a new one
                    $newShipment->getItemsCollection();
                    // a little hack to force magento add item into shipment items collection
                    $sItemId = $sItem->getId();
                    $sItem->setId(null);
                    $newShipment->addItem($sItem);
                    $sItem->setId($sItemId);
                    // remember the shipment to save and send notification
                    $newShipment->setUdmultiSave(true)->setUdmultiSend(true);
                    // old save is in the internal loop to make sure that it's skipped when dup safeguard is triggered
                    $oldShipment->setUdmultiSave(true);
                    $shippedItemIds[] = $orderItemId;
                }
            }
            $sendNotifications = array();
            foreach ($shipments as $s) {
                if (!$s->getUdmultiSave()) {
                    continue;
                }
                if (count($s->getAllItems())>0) {
                    $save->addObject($s);
                    if ($s->getUdmultiSend()) {
                        $sendNotifications[] = $s;
                    }
                } else {
                    // if any shipments/vendors have no more products, delete them
                    $delete->addObject($s);
                }
            }

            // commit transactions
            $save->save();
            $delete->delete();

            $vendorRates = array();
            $shippingMethod = explode('_', $order->getShippingMethod(), 2);
            $shippingDetails = $order->getUdropshipShippingDetails();
            $details = Zend_Json::decode($shippingDetails);
            if (!empty($details) && !empty($shippingMethod[1])) {
                if (!empty($details['methods'][$shippingMethod[1]])) {
                    $vendorRates = &$details['methods'][$shippingMethod[1]]['vendors'];
                } elseif (!empty($details['methods'])) {
                    $vendorRates = &$details['methods'];
                }
            }

            foreach ($vendors as $orderItemId=>$vData) {
                if (in_array($orderItemId, $shippedItemIds)) continue;
                if (empty($vendorRates[$vData['id']])) {
                    list($carrierTitle, $methodTitle) = explode('-', $vData['method_name'], 2);
                    $vendorRates[$vData['id']] = array(
                        'cost'  => 0,
                        'price' => 0,
                        'code'  => $vData['method'],
                        'carrier_title' => @$carrierTitle,
                        'method_title'  => @$methodTitle
                    );
                }
            }

            $order->setUdropshipShippingDetails(Zend_Json::encode($details));
            $order->getResource()->saveAttribute($order, 'udropship_shipping_details');

            // send pending notifications
            foreach ($sendNotifications as $s) {
                Mage::helper('udropship')->sendVendorNotification($s);
            }
            Mage::helper('udropship')->processQueue();
        } else {
            $save->save();
        }

        return $changed;
    }

	public function saveThisVendorProducts($data, $v)
    {
        return $this->_saveVendorProducts($data, false, $v);
    }
    public function saveVendorProducts($data)
    {
        return $this->_saveVendorProducts($data, false);
    }
	public function saveThisVendorProductsPidKeys($data, $v)
    {
        return $this->_saveVendorProducts($data, true, $v);
    }
    public function saveVendorProductsPidKeys($data)
    {
        return $this->_saveVendorProducts($data, true);
    }
    protected $_reindexFlag=true;
    public function setReindexFlag($flag)
    {
        $this->_reindexFlag=$flag;
        return $this;
    }
    protected function _saveVendorProducts($data, $pidKeys=false, $v=null)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        if (is_null($v)) {
        	$v = Mage::getSingleton('udropship/session')->getVendor();
        }

        $res = Mage::getSingleton('core/resource');
        $write = $res->getConnection('udropship_write');
        $table = $res->getTableName('udropship/vendor_product');

        $select = $write->select()
            ->from($table)
            ->where('vendor_id=?', $v->getId());
        if ($pidKeys) {
            $select->where('product_id in (?)', array_keys($data));
        } else {
            $select->where('vendor_product_id in (?)', array_keys($data));
        }

        $products = $write->fetchAll($select);
        if (!$products) {
            return false;
        }
        $allUpdate = $updatePids = $allUpdateFields = array();
        $cnt = 0;
        $fields = array('vendor_sku'=>0, 'vendor_cost'=>1, 'stock_qty'=>1, 'priority'=>1);
        foreach ($products as $p) {
            $vpSK = $pidKeys ? 'product_id' : 'vendor_product_id';
            if (empty($data[$p[$vpSK]])) continue;
            $new = $data[$p[$vpSK]];
            $update = array();
            if (array_key_exists('stock_qty_add', $new) && !array_key_exists('stock_qty', $new)) {
                $new['stock_qty'] = $p['stock_qty']+$new['stock_qty_add'];
            }
            foreach ($fields as $k=>$numeric) {
            	if (!array_key_exists($k, $new)) continue;
                if (!is_null($p[$k]) && $numeric) {
                    $p[$k] *= 1.0;
                }
                if (!isset($new[$k]) || $new[$k]==='') {
                    $new[$k] = null;
                } elseif ($numeric) {
                    $new[$k] *= 1.0;
                }
                if ($p[$k]!==$new[$k]) {
                    $update[$k] = $new[$k];
                }
            }
            if (!empty($update)) {
                //$write->update($table, $update, 'vendor_product_id='.$p['vendor_product_id']);
                $update['vendor_product_id'] = $p['vendor_product_id'];
                $allUpdate[$p[$vpSK]] = $update;
                $allUpdateFields = array_merge($allUpdateFields, $update);
                if (array_intersect_key($update, array('stock_qty'=>1,'status'=>1,'avail_state'=>1,'avail_date'=>1))) {
                    $updatePids[$p['product_id']] = $p['product_id'];
                    /*
                    Mage::helper('udmulti')->getUdmultiStock($p['product_id'], true);
                    Mage::getModel('cataloginventory/stock_item')
                        ->loadByProduct($p['product_id'])
                        ->setData('__dummy',1)
                        ->save();
                    */
                }
                $cnt++;
            }
        }
        foreach ($allUpdate as $pidKey=>&$update) {
            $_update = array();
            foreach ($allUpdateFields as $uf=>$dummy) {
                if (!array_key_exists($uf, $update)) {
                    $update[$uf] = @$data[$pidKey][$uf];
                }
                $_update[$uf] = $update[$uf];
            }
            $update = $_update;
        }
        unset($update);
        if (!empty($allUpdate)) {
            unset($allUpdateFields['vendor_product_id']);
            Mage::getResourceSingleton('udropship/helper')->multiInsertOnDuplicate(
                $table, $allUpdate, array_keys($allUpdateFields)
            );
        }
        if (!empty($updatePids)) {
            $siData = Mage::getResourceSingleton('udropship/helper')->loadDbColumns(
                Mage::getModel('cataloginventory/stock_item'),
                array('product_id'=>$updatePids),
                array('backorders','use_config_backorders')
            );
            $mvData = $this->getMultiVendorData($updatePids, false, true);
            $method = Mage::getStoreConfig('udropship/stock/total_qty_method');
            $mvQty = $isInStock = array();
            foreach ($updatePids as $pid) {
                $mvQty[$pid] = 0;
                $isInStock[$pid] = false;
            }
            foreach ($mvData as $mv) {
                $mvPid = $mv->getProductId();
                $mvVid = $mv->getVendorId();
                if (!array_key_exists($mv->getProductId(), $mvQty)) {
                    $mvQty[$mvPid] = 0;
                    $isInStock[$mvPid] = false;
                }
                $vQty = $mv->getStockQty();
                $vQty = is_null($vQty) || $vQty==='' ? 10000 : $vQty;
                if ($method=='sum') {
                    $mvQty[$mvPid] += max($vQty, 0);
                } else {
                    $mvQty[$mvPid] = max($mvQty[$mvPid], $vQty);
                }
                $isInStock[$mvPid] = $isInStock[$mvPid] || $mvQty[$mvPid]>0;
            }
            if (!empty($isInStock)) {
                $updateStock = array();
                foreach ($isInStock as $pid=>$iis) {
                    $updateStock[] = array(
                        'product_id' => $pid,
                        'is_in_stock' => $iis,
                        'qty' => $mvQty[$pid],
                        'stock_id' => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID
                    );
                }
                Mage::getResourceSingleton('udropship/helper')->multiInsertOnDuplicate(
                    'cataloginventory/stock_item',
                    $updateStock, array('is_in_stock','qty')
                );
                if ($this->_reindexFlag) {
                    $indexer = Mage::getSingleton('index/indexer');
                    $pAction = Mage::getModel('catalog/product_action');
                    $idxEvent = Mage::getModel('index/event')
                        ->setEntity(Mage_Catalog_Model_Product::ENTITY)
                        ->setType(Mage_Index_Model_Event::TYPE_MASS_ACTION)
                        ->setDataObject($pAction);
                    /* hook to cheat index process to be executed */
                    $pAction->setWebsiteIds(array(0));
                    $pAction->setProductIds($updatePids);
                    $indexer->getProcessByCode('cataloginventory_stock')->register($idxEvent)->processEvent($idxEvent);
                    $indexer->getProcessByCode('catalog_product_price')->register($idxEvent)->processEvent($idxEvent);
                }
            }
        }
        return $cnt;
    }

    public function getVendorSelect($data)
    {
        $html = '<select name="'.@$data['name'].'" id="'.@$data['id'].'" class="'
            .@$data['class'].'" title="'.@$data['title'].'" '.@$data['extra'].' onchange="try{if (this.selectedIndex>-1) {$(\''.@$data['cost_id'].'\').value=this.options[this.selectedIndex].title}}catch(e){}">';
        if (is_array($data['options'])) {
            foreach ($data['options'] as $vId => $opt) {
                $selectedHtml = $vId == @$data['selected'] ? ' selected="selected"' : '';
                $html .= '<option value="'.$vId.'" title="'.@$opt['cost'].'" '.$selectedHtml.'>'.$this->htmlEscape(@$opt['name']).'</option>';
            }
        }
        $html.= '</select>';
        $html.= '<input type="hidden" name="'.@$data['cost_name'].'" id="'.@$data['cost_id'].'" value="'.@$data['options'][@$data['selected']]['cost'].'" class="'.@$data['cost_class'].'" />';
        return $html;
    }

    public function attachMultivendorData($products, $isActive, $reload=false)
    {
        $pIds = array();
        foreach ($products as $product) {
            if ($product->hasUdmultiStock() && !$reload || !$product->getId()) {
                if ($product->getStockItem()) {
                    $product->getStockItem()->setUdmultiStock($product->getUdmultiStock());
                    $product->getStockItem()->setUdmultiAvail($product->getUdmultiAvail());
                }
                continue;
            }
            $pIds[] = $product->getId();
        }
        //$loadMethod = $isActive ? 'getActiveMultiVendorData' : 'getMultiVendorData';
        $loadMethod = 'getMultiVendorData';
        $vendorData = Mage::helper('udmulti')->$loadMethod($pIds);
        foreach ($products as $product) {
            if ($product->hasUdmultiStock() && !$reload || !$product->getId()) continue;
            $udmData = $udmAvail = $udmStock = array();
            foreach ($vendorData as $vp) {
                if ($vp->getProductId() != $product->getId()) continue;
                $udmStock[$vp->getVendorId()] = $vp->getStockQty();
                $udmData[$vp->getVendorId()] = $vp->getData();
                $udmAvail[$vp->getVendorId()] = array(
                    'avail_state' => $vp->getData('avail_state'),
                    'avail_date' => $vp->getData('avail_date'),
                    'status' => $vp->getData('status'),
                );
            }
            $product->setMultiVendorData($udmData);
            $product->setAllMultiVendorData($udmData);
            $product->setUdmultiStock($udmStock);
            $product->setUdmultiAvail($udmAvail);
            if ($product->getStockItem()) {
                $product->getStockItem()->setUdmultiStock($udmStock);
                $product->getStockItem()->setUdmultiAvail($udmAvail);
            }
            if (0 && $isActive && Mage::getStoreConfigFlag('udropship/stock/hide_out_of_stock')) {
                $vendorsToHide = array();
                foreach ($udmData as $vId=>$dummy) {
                    if (!Mage::helper('udmulti')->isSalableByVendorData($product, $vId, $dummy)) {
                        $vendorsToHide[$vId] = $vId;
                    }
                }
                if (!empty($vendorsToHide)) {
                    foreach ($vendorsToHide as $vId) {
                        unset($udmStock[$vId], $udmData[$vId], $udmAvail[$vId]);
                    }
                    $product->setMultiVendorData($udmData);
                    $product->setUdmultiStock($udmStock);
                    $product->setUdmultiAvail($udmAvail);
                    if ($product->getStockItem()) {
                        $product->getStockItem()->setUdmultiStock($udmStock);
                        $product->getStockItem()->setUdmultiAvail($udmAvail);
                    }
                }
            }
            /*
            if (Mage::helper('udropship')->isModuleActive('udmultiprice')) {
                $minPrice = $product->getFinalPrice();
                foreach ($udmData as $vp) {
                    if (null !== $vp['vendor_price']) {
                        Mage::helper('udmultiprice')->useVendorPrice($product, $vp);
                        $minPrice = min($minPrice, $product->getFinalPrice());
                        Mage::helper('udmultiprice')->revertVendorPrice($product);
                    }
                }
                $product->setUdmultiBestPrice($minPrice);
            }
            */
        }
        return $this;
    }
}
