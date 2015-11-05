<?php
/**
 * Unirgy_NotifyVendor extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Unirgy
 * @package    Unirgy_NotifyVendor
 * @copyright  Copyright (c) 2008 Unirgy LLC
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Unirgy
 * @package    Unirgy_NotifyVendor
 * @author     Boris (Moshe) Gurevich <moshe@unirgy.com>
 */
class Unirgy_NotifyVendor_Model_Observer
{
    public function sales_order_invoice_pay($observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        $storeId = $order->getStoreId();
        $items = $invoice->getAllItems();

        $productIds = array();
        foreach ($items as $id=>$item) {
            $productIds[$item->getProductId()] = $id;
        }

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', array('in'=>array_keys($productIds)))
            ->addAttributeToFilter('unotify_vendor', array('notnull'=>true))
            ->addStoreFilter($order->getStore())
            ->addAttributeToSelect('name');

        if (!$products->count()) {
            return;
        }
        $vendorItems = array();
        foreach ($products as $product) {
            $item = $items[$productIds[$product->getId()]];
            $item->setProduct($product);
            $vendorItems[$product->getUnotifyVendor()][] = $item;
        }

        $this->setDesignStore($storeId);

        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId));

        $eav = Mage::getSingleton('eav/config');
        $attr = $eav->getAttribute('catalog_product', 'unotify_vendor');

        $template = Mage::getStoreConfig('unotifyvendor/email/template', $storeId);
        $identity = Mage::getStoreConfig('unotifyvendor/email/identity', $storeId);

        foreach ($vendorItems as $vendorId=>$items) {
            $vendor = $attr->getSource()->getOptionText($vendorId);
            if (!preg_match('#^"?([^<]+)"?\s+<([^>]+)>\s*$#', $vendor, $m)) {
                continue;
            }

            $vendorName = $m[1];
            $vendorEmail = $m[2];

            $data = array(
                'order'           => $order,
                'store_name'      => Mage::app()->getStore($storeId)->getName(),
                'vendor_name'     => $vendorName,
                'order_id'        => $order->getIncrementId(),
                'items'           => $items,
            );

            $mailTemplate->sendTransactional($template, $identity, $vendorEmail, $vendorName, $data);
        }

        $this->setDesignStore();
    }
    
    protected $_store;
    protected $_oldStore;
    protected $_oldArea;
    protected $_oldDesign;

    public function setDesignStore($store=null)
    {
        if (!is_null($store)) {
            if ($this->_store) {
                return $this;
            }
            $this->_oldStore = Mage::app()->getStore();
            $this->_oldArea = Mage::getDesign()->getArea();
            $this->_store = Mage::app()->getStore($store);

            $store = $this->_store;
            $area = 'frontend';
            $package = Mage::getStoreConfig('design/package/name', $store);
            $design = array('package'=>$package, 'store'=>$store->getId());
            $inline = false;
        } else {
            if (!$this->_store) {
                return $this;
            }
            $this->_store = null;
            $store = $this->_oldStore;
            $area = $this->_oldArea;
            $design = $this->_oldDesign;
            $inline = true;
        }

        Mage::app()->setCurrentStore($store);
        $oldDesign = Mage::getDesign()->setArea($area)->setAllGetOld($design);
        Mage::app()->getTranslator()->init($area, true);
        Mage::getSingleton('core/translate')->setTranslateInline($inline);

        if ($this->_store) {
            $this->_oldDesign = $oldDesign;
        } else {
            $this->_oldStore = null;
            $this->_oldArea = null;
            $this->_oldDesign = null;
        }

        return $this;
    }
}