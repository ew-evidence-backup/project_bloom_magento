<?php

class Unirgy_DropshipMulti_Block_Adminhtml_Order_Js extends Mage_Adminhtml_Block_Template
{
    protected $_store = null;
    protected $_vendors = null;
    protected $_stockCollection = array();
    protected $_vMethods = array();
    protected $_extraOrderVendors = array();
    protected $_reassignSkipStockcheck;
    protected $_items = array();
    protected $_itemsById = array();
    protected $_stockChecked = false;

    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function initItems($order)
    {
        $this->_items = $order->getAllItems();
        $stockChecked = false;
        foreach ($this->_items as $item) {
            $stockChecked = $stockChecked || $item->hasData('_udropship_stock_levels');
            $this->_itemsById[$item->getId()] = $item;
        }
        if (!$stockChecked) {
            //$this->checkStockAvailability($order);
        }
        return $this;
    }

    public function checkStockAvailability($order)
    {
        $items = Mage::getModel('sales/order')->load($order->getId())->getAllItems();
        foreach ($items as $_item) {
            $_item->setUdpoCreateQty(
                Mage::helper('udropship')->getOrderItemById($order, $_item->getId())->getUdpoCreateQty()
            );
        }

        Mage::helper('udropship/protected')->reassignApplyStockAvailability($items);

        foreach ($items as $item) {
            Mage::helper('udropship')->getOrderItemById($order, $item->getId())->setData(
                '_udropship_stock_levels', $item->getData('udropship_stock_levels')
            );
        }
        return $this;
    }

    protected function _initVendors($reload=false)
    {
        if (is_null($this->_vendors) || $reload) {
            $order = $this->getOrder();
            $this->initItems($order);
            $this->_stockCollection = Mage::helper('udmulti')->getMultiVendorData($this->_items, true);
            $this->_vendors = array();
            $this->_store = Mage::app()->getStore($order->getStoreId());
            $this->_reassignSkipStockcheck = Mage::getStoreConfigFlag('udropship/stock/reassign_skip_stockcheck', $this->_store);
            $this->_extraOrderVendors = Mage::helper('udropship')->getSalesEntityVendors($order);
            foreach ($this->_items as $item) {
                $parentItem = $item->getParentItem();
                if ($parentItem && $parentItem->getProductType()=='configurable') {
                    $this->_getItemVendors($item, $parentItem->getId());
                    continue;
                }
                $this->_getItemVendors($item);
            }
            $this->_filterVendors();
            $this->_initVendorShippingMethods();
        }
        return $this;
    }

    public function getVendorsJson()
    {
        $this->_initVendors();
        return Zend_Json::encode($this->_vendors);
    }

    public function getItemVendorSelect($itemId, $data)
    {
        $this->_initVendors();
        $data['options'] = @$this->_vendors[$itemId]['all'];
        $data['selected'] = @$this->_vendors[$itemId]['current'];
        return Mage::helper('udmulti')->getVendorSelect($data);
    }

    protected function _filterVendors()
    {
        if (Mage::getStoreConfig('udropship/stock/manual_udpo_hide_failed_vendors', $this->_store)) {
            foreach ($this->_vendors as $itemId => &$vData) {
                $unsVids = array();
                foreach ($vData['all'] as $vId => $dummy) {
                    $item = $this->_itemsById[$itemId];
                    if ($vId != $vData['current'] && $item->hasData('_udropship_stock_levels')) {
                        if ($item->getProductType()=='configurable') {
                            $children = $item->getChildrenItems() ? $item->getChildrenItems() : $item->getChildren();
                            foreach ($children as $child) {
                                if (!$child->getData("_udropship_stock_levels/$vId/status")
                                    && $child->getUdropshipVendor()!=$vId
                                ) {
                                    $unsVids[] = $vId;
                                }
                                break;
                            }
                        } else {
                            if (!$item->getData("_udropship_stock_levels/$vId/status")
                                && !$item->getUdropshipVendor()!=$vId
                            ) {
                                $unsVids[] = $vId;
                            }
                        }
                    }
                }
                foreach ($unsVids as $vId) {
                    unset($vData['all'][$vId]);
                }
            }
        }
        return $this;
    }

    protected function _getItemVendors($item, $itemId=null)
    {
        $hlp = Mage::helper('udropship');
        $itemId = !is_null($itemId) ? $itemId : $item->getId();
        $priceItem = (($parentItem = $item->getParentItem()) && $parentItem->getProductType()=='configurable') ? $parentItem : $item;
        $currentVendor = $item->hasUdpoUdropshipVendor() ? $item->getUdpoUdropshipVendor() : $item->getUdropshipVendor();
        $this->_vendors[$itemId]['current'] = $currentVendor;
        foreach ($this->_stockCollection as $vp) {
            $v = $hlp->getVendor($vp->getVendorId());
            if ($vp->getProductId()==$item->getProductId()
                && ($vp->getVendorId()==$currentVendor
                    || is_null($vp->getStockQty())
                    || $vp->getStockQty()>=$item->getQtyOrdered()
                    || $v->getStockcheckCallback()
                    || $this->_reassignSkipStockcheck
                )
            ) {
                if (empty($this->_vMethods[$vp->getVendorId()])) {
                    $this->_vMethods[$vp->getVendorId()] = array();
                }
                $this->_vendors[$itemId]['all'][$vp->getVendorId()] = array(
                    'name' => $this->htmlEscape($vp->getVendorName()).' - '.$this->_store->formatPrice($hlp->getItemBaseCost($priceItem, $vp->getVendorCost()), false),
                    'cost' => $hlp->getItemBaseCost($priceItem, $vp->getVendorCost()),
                    'methods' => &$this->_vMethods[$vp->getVendorId()]
                );
            }
        }
        if (empty($this->_vendors[$itemId]['current'])) {
            if (!empty($this->_vendors[$itemId]['all'])) {
                reset($this->_vendors[$itemId]['all']);
                $currentVendor = $this->_vendors[$itemId]['current'] = key($this->_vendors[$itemId]['all']);
            }
        }
        if (empty($this->_vendors[$itemId]['all'][$currentVendor])) {
            $v = $hlp->getVendor($currentVendor);
            $this->_vendors[$itemId]['all'][$currentVendor] = array(
                'name' => $this->htmlEscape($v->getVendorName()).' - '.$this->_store->formatPrice($hlp->getItemBaseCost($priceItem), false),
                'cost' => $hlp->getItemBaseCost($priceItem),
                'methods' => &$this->_vMethods[$currentVendor]
            );
        }
        if (!empty($this->_extraOrderVendors[$itemId])) {
            foreach ($this->_extraOrderVendors[$itemId] as $vId=>$_dummy) {
                if (empty($this->_vendors[$itemId]['all'][$vId])) {
                    $v = $hlp->getVendor($vId);
                    $this->_vendors[$itemId]['all'][$vId] = array(
                        'name' => $this->htmlEscape($v->getVendorName()).' - '.$this->_store->formatPrice($hlp->getItemBaseCost($priceItem), false),
                        'cost' => $hlp->getItemBaseCost($priceItem),
                        'methods' => &$this->_vMethods[$vId]
                    );
                }
            }
        }

    }

    protected function _initVendorShippingMethods()
    {
        Mage::helper('udropship')->initVendorShippingMethodsForHtmlSelect($this->getOrder(), $this->_vMethods);
    }
}