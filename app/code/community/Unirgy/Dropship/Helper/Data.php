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

class Unirgy_Dropship_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
    * Vendors cache
    *
    * @var array
    */
    protected $_vendors = array();

    /**
    * Regions cache
    *
    * @var array
    */
    protected $_regions = array();

    /**
    * Writable flag to show true stock status or not
    *
    * @var boolean
    */
    protected $_trueStock = false;

    /**
    * Collection of order shipments for the vendor interface
    *
    * @var mixed
    */
    protected $_vendorShipmentCollection;

    /**
    * Carrier Methods Cache
    *
    * @var array
    */
    protected $_carrierMethods = array();

    protected $_version;

    protected $_localVendorId;

    protected $_isActive;

    public function getVersion()
    {
        if (!$this->_version) {
            $this->_version = (string)Mage::getConfig()->getNode('modules/Unirgy_Dropship/version');
        }
        return $this->_version;
    }

    public function isActive($store=null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if (isset($this->_isActive[$storeId])) {
            return $this->_isActive[$storeId];
        }
        if (!extension_loaded('ionCube Loader')) {
            return ($this->_isActive[$storeId] = false);
        }
        if ($this->isModuleActive('Unirgy_SimpleLicense')) {
            try {
                Unirgy_Dropship_Helper_Protected::validateLicense('Unirgy_Dropship');
            } catch (Unirgy_SimpleLicense_Exception $e) {
                return ($this->_isActive[$storeId] = false);
            }
        } else {
            if (!Mage::helper('udropship/protected')->validateLicense('Unirgy_Dropship')) {
                return ($this->_isActive[$storeId] = false);
            }
        }
        $udropship = Mage::getStoreConfigFlag('carriers/udropship/active', $store);
        $udsplit = Mage::getStoreConfigFlag('carriers/udsplit/active', $store);
        return ($this->_isActive[$storeId] = $udropship || $udsplit);
    }

    public function isModuleActive($code)
    {
        $module = Mage::getConfig()->getNode("modules/$code");
        $model = Mage::getConfig()->getNode("global/models/$code");
        return $module && $module->is('active') || $model;
    }

    public function isUdpayoutActive()
    {
        return Mage::helper('udropship')->isModuleActive('Unirgy_DropshipPayout');
    }

    public function isUdpoActive()
    {
        return $this->isModuleActive('Unirgy_DropshipPo')
            && $this->hasMageFeature('sales_flat') && Mage::helper('udpo')->isActive();
    }

    public function isUdmultiActive()
    {
        return Mage::helper('udropship')->isModuleActive('Unirgy_DropshipMulti') && Mage::helper('udmulti')->isActive();
    }

    public function isUdmultiAvailable()
    {
        return ($multi = Mage::getConfig()->getNode('modules/Unirgy_DropshipMulti')) && $multi->is('active');
    }

    public function isUdpoMpsAvailable($carrierCode, $vendor=null)
    {
        return in_array($carrierCode, array('fedex')) && Mage::helper('udropship')->isModuleActive('Unirgy_DropshipPoMps');
    }
    public function checkVendorProductName($data = array())
    {
        $vendor = Mage::getSingleton('udropship/session')->getVendor();
        $p = Mage::getModel('catalog/product')->getCollection()->addAttributetoFilter('name',array('eq'=>$data['name']))->getAllIds();

        $p = array_flip($p);
        unset($p[$data['id']]);
        $p = array_flip($p);

        $products = $vendor->getAssociatedProducts($p);

        if(count($p) && count($products)>0){
            $res['status'] = 'false';
            $res['msg'] = 'Product name already exist';
            //$res[] = count($products);
        }else{
            $res['status'] = 'true';
            $res['msg'] = '';
        }
        echo json_encode($res,JSON_FORCE_OBJECT);
    }
    /**
    * Retrieve local vendor id
    *
    * @param integer $store
    */
    public function getLocalVendorId($store=null)
    {
        if (is_null($this->_localVendorId)) {
            $this->_localVendorId = Mage::getStoreConfig('udropship/vendor/local_vendor', $store);
            // can't proceed if not configured
            if (!$this->_localVendorId) {
                #Mage::throwException('Local vendor is not set, please configure correctly');
                $this->_localVendorId = 0;
            }
        }
        return $this->_localVendorId;
    }

    /**
    * Get vendor object for vendor ID or Name and cache it
    *
    * If argument is product model, get udropship_vendor value
    *
    * @param integer|string|Mage_Catalog_Model_Product $id
    * @return Unirgy_Dropship_Model_Vendor
    */
    public function getVendor($id)
    {
        if ($id instanceof Unirgy_Dropship_Model_Vendor) {
            if (empty($this->_vendors[$id->getId()])) {
                $this->_vendors[$id->getId()] = $id;
            }
            return $id;
        }
        if ($id instanceof Mage_Catalog_Model_Product) {
            $id = $this->getProductVendorId($id);
        }
        $vendor = Mage::getModel('udropship/vendor');
        if (empty($id)) {
            return $vendor;
        }
        if (empty($this->_vendors[$id])) {
            if (!is_numeric($id)) {
                $vendor->load($id, 'vendor_name');
                if ($vendor->getId()) {
                    $this->_vendors[$vendor->getId()] = $vendor;
                }
            } else {
                $vendor->load($id);
                if ($vendor->getId()) {
                    $this->_vendors[$vendor->getVendorName()] = $vendor;
                }
            }
            $this->_vendors[$id] = $vendor;
        }
        return $this->_vendors[$id];
    }

    public function getVendorName($id)
    {
        $v = $this->getVendor($id);
        if ($v->getId()) {
            return $v->getVendorName();
        }
        return false;
    }

    public function getVendorDecisionModel()
    {

    }

    public function getVendorForgotPasswordUrl()
    {
        return Mage::getUrl('udropship/vendor/password');
    }

    /**
    * Get shipment status name from shipment object
    *
    * @param Mage_Sales_Model_Order_Shipment $shipment
    * @return string
    */
    public function getShipmentStatusName($shipment)
    {
        $statuses = Mage::getSingleton('udropship/source')->setPath('shipment_statuses')->toOptionHash();
        $id = $shipment instanceof Mage_Sales_Model_Order_Shipment ? $shipment->getUdropshipStatus() : $shipment;
        return isset($statuses[$id]) ? $statuses[$id] : 'Unknown';
    }

    /**
    * Get shipment method name from shipment object
    *
    * @param Mage_Sales_Model_Order_Shipment $shipment
    * @param boolean $full whether to prefix with carrier name
    * @return string
    */
    public function getShipmentMethodName($shipment, $full=false)
    {
        $vendor = $this->getVendor($shipment->getUdropshipVendor());
        $method = $shipment->getOrder()->getShippingMethod();
        return $vendor->getShippingMethodName($method, $full);
    }

    /**
    * Return vendor ID for a product object
    *
    * @param mixed $product
    * @param boolean $forceReal
    */
    public function getProductVendorId($product, $forceReal=false)
    {
        $storeId = $product->getStoreId();
        $localVendorId = $this->getLocalVendorId($storeId);
        $vendorId = $product->getUdropshipVendor();

        // product doesn't have vendor specified
        if (!$vendorId) {
            return $localVendorId;
        }
        // force real product vendor
        if ($forceReal) {
            return $vendorId;
        }

        // all other cases
        return $vendorId;
    }

    /**
    * Return vendor ID for quote item based on requested qty
    *
    * if $qty===true, always return dropship vendor id
    * if $qty===false, always return local vendor id
    * otherwise return local vendor if enough qty in stock
    *
    * @param Mage_Sales_Model_Quote_Item $item
    * @param integer|boolean $qty
    * @return integer
    * @deprecated since 1.6.0
    */
    public function getQuoteItemVendor($item, $qty=0)
    {
        $product = $item->getProduct();
        if (!$product || !$product->hasUdropshipVendor()) {
            // if not available, load full product info to get product vendor
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
        }
        $store = $item->getQuote() ? $item->getQuote()->getStore() : $item->getOrder()->getStore();

        $localVendorId = $this->getLocalVendorId($store);
        $vendorId = $product->getUdropshipVendor();
        // product doesn't have vendor specified OR force local vendor
        if (!$vendorId || $qty===false) {
            return $localVendorId;
        }
        // force real vendor
        if ($qty===true) {
            return $vendorId;
        }

        // local stock is available
        if (Mage::getSingleton('udropship/stock_availability')->getUseLocalStockIfAvailable($store, $vendorId) && $product->getStockItem()->checkQty($qty)) {
            return $localVendorId;
        }

        // all other cases
        return $vendorId;
    }

    /**
    * Get vendors collection for quote items
    *
    * @deprecated
    * @param Mage_Sales_Model_Mysql4_Quote_Item_Collection $items
    * @return Unirgy_Dropship_Mysql4_Vendor_Collection
    */
    public function collectQuoteItemsVendors($items)
    {
        $productQtys = array();
        foreach ($items as $item) {
            $id = $item->getProductId();
            if (isset($productQtys[$id])) {
                $productQtys[$id] += $item->getQty();
            } else {
                $productQtys[$id] = $item->getQty();
            }
        }
        $vendors = Mage::getModel('udropship/vendor')->getCollection()
            ->addProductFilter(array_keys($productQtys), 1);
        return $vendors;
    }

    /**
    * Mark shipment as complete and shipped
    *
    * @param Mage_Sales_Model_Order_Shipment $shipment
    */
    public function setShipmentComplete($shipment)
    {
        $this->completeShipment($shipment, true);
        $this->completeUdpoIfShipped($shipment, true);
        $this->completeOrderIfShipped($shipment, true);
        return $this;

        $items = array();
        $order = $shipment->getOrder();#Mage::getModel('sales/order')->load($shipment->getOrderId());
        $orderItems = $order->getItemsCollection();
        foreach ($shipment->getAllItems() as $item) {
            $orderItem = Mage::helper('udropship')->getOrderItemById($order, $item->getOrderItemId());
            $orderItem->setQtyShipped($orderItem->getQtyOrdered());
        }
        $shipment->setUdropshipStatus(Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_SHIPPED);
        Mage::helper('udropship')->addShipmentComment(
            $shipment,
            Mage::helper('udropship')->__('Marked as shipped')
        );

        $transaction = Mage::getModel('core/resource_transaction');
        foreach ($orderItems as $item) {
            $transaction->addObject($item);
        }
        $transaction->addObject($shipment)->save();

        return $this;
    }

    public function sendPasswordResetEmail($email)
    {
        $vendor = Mage::getModel('udropship/vendor')->load($email, 'email');
        if (!$vendor->getId()) {
            return $this;
        }
        $vendor->setRandomHash(sha1(rand()))->save();

        $store = Mage::app()->getStore();
        $this->setDesignStore($store);
        Mage::getModel('core/email_template')->sendTransactional(
            $store->getConfig('udropship/vendor/vendor_password_template'),
            $store->getConfig('udropship/vendor/vendor_email_identity'),
            $email, $email, array(
                'store_name' => $store->getName(),
                'vendor_name' => $vendor->getVendorName(),
                'url' => Mage::getUrl('udropship/vendor/password', array(
                    'confirm' => $vendor->getRandomHash(),
                ))
            )
        );
        $this->setDesignStore();

        return $this;
    }

    /**
    * Send notification to vendor about new order
    *
    * @param Mage_Sales_Model_Order_Shipment $shipment
    */
    public function sendVendorNotification($shipment)
    {
        $vendor = $this->getVendor($shipment->getUdropshipVendor());
        $method = $vendor->getNewOrderNotifications();

        if (!$method || $method=='0') {
            return $this;
        }

        $data = compact('vendor', 'shipment', 'method');
        if ($method=='1') {
            $vendor->sendOrderNotificationEmail($shipment);
        } else {
            $config = Mage::getConfig()->getNode('global/udropship/notification_methods/'.$method);
            if ($config) {
                $cb = explode('::', (string)$config->callback);
                $obj = Mage::getSingleton($cb[0]);
                $method = $cb[1];
                $obj->$method($data);
            }
        }
        Mage::dispatchEvent('udropship_send_vendor_notification', $data);

        return $this;
    }

    public function sendShipmentCommentNotificationEmail($shipment, $comment)
    {
        $order = $shipment->getOrder();
        $store = $order->getStore();

        $vendor = $this->getVendor($shipment->getUdropshipVendor());

        $hlp = Mage::helper('udropship');
        $data = array();

        $hlp->setDesignStore($store);

        $data += array(
            'shipment'        => $shipment,
            'order'           => $order,
            'vendor'          => $vendor,
            'comment'         => $comment,
            'store_name'      => $store->getName(),
            'vendor_name'     => $vendor->getVendorName(),
            'shipment_id'     => $shipment->getIncrementId(),
            'shipment_status' => $this->getShipmentStatusName($shipment),
            'order_id'        => $order->getIncrementId(),
            'shipment_url'    => Mage::getUrl('udropship/vendor/', array('_query'=>'filter_order_id_from='.$order->getIncrementId().'&filter_order_id_to='.$order->getIncrementId())),
            'packingslip_url' => Mage::getUrl('udropship/vendor/pdf', array('shipment_id'=>$shipment->getId())),
        );

        if ($this->isUdpoActive() && ($po = Mage::helper('udpo')->getShipmentPo($shipment))) {
            $data['po']     = $po;
            $data['po_id']  = $po->getIncrementId();
            $data['po_url'] = Mage::getUrl('udpo/vendor/', array('_query'=>'filter_po_id_from='.$po->getIncrementId().'&filter_po_id_to='.$po->getIncrementId()));
        }

        $template = $store->getConfig('udropship/vendor/shipment_comment_vendor_email_template');
        $identity = $store->getConfig('udropship/vendor/vendor_email_identity');

        $data['_BCC'] = $vendor->getNewOrderCcEmails();
        if (($emailField = $store->getConfig('udropship/vendor/vendor_notification_field'))) {
            $email = $vendor->getData($emailField) ? $vendor->getData($emailField) : $vendor->getEmail();
        } else {
            $email = $vendor->getEmail();
        }
        Mage::getModel('udropship/email')->sendTransactional($template, $identity, $email, $vendor->getVendorName(), $data);

        $hlp->setDesignStore();
    }

    /**
    * Send vendor comment to store owner
    *
    * @param Mage_Sales_Model_Order_Shipment $shipment
    * @param string $comment
    */
    public function sendVendorComment($shipment, $comment)
    {
        $order = $shipment->getOrder();
        $store = $order->getStore();
        $to = $store->getConfig('udropship/admin/vendor_comments_receiver');
        $subject = $store->getConfig('udropship/admin/vendor_comments_subject');
        $template = $store->getConfig('udropship/admin/vendor_comments_template');
        $vendor = $this->getVendor($shipment->getUdropshipVendor());
        $ahlp = Mage::getModel('adminhtml/url');

        if ($subject && $template && $vendor->getId()) {
            $toEmail = $store->getConfig('trans_email/ident_'.$to.'/email');
            $toName = $store->getConfig('trans_email/ident_'.$to.'/name');
            $data = array(
                'vendor_name'   => $vendor->getVendorName(),
                'order_id'      => $order->getIncrementId(),
                'shipment_id'   => $shipment->getIncrementId(),
                'vendor_url'    => $ahlp->getUrl('udropship/adminhtml_vendor/edit', array(
                    'id'        => $vendor->getId()
                )),
                'order_url'     => $ahlp->getUrl('adminhtml/sales_order/view', array(
                    'order_id'  => $order->getId()
                )),
                'shipment_url'  => $ahlp->getUrl('adminhtml/sales_order_shipment/view', array(
                    'shipment_id'=> $shipment->getId(),
                    'order_id'  => $order->getId(),
                )),
                'comment'      => $comment,
            );
            if ($this->isUdpoActive() && ($po = Mage::helper('udpo')->getShipmentPo($shipment))) {
                $data['po_id'] = $po->getIncrementId();
                $data['po_url'] = $ahlp->getUrl('udpoadmin/order_po/view', array(
                    'udpo_id'  => $po->getId(),
                    'order_id' => $order->getId(),
                ));
                $template = preg_replace('/{{isPoAvailable}}(.*?){{\/isPoAvailable}}/s', '\1', $template);
            } else {
                $template = preg_replace('/{{isPoAvailable}}.*?{{\/isPoAvailable}}/s', '', $template);
            }
            foreach ($data as $k=>$v) {
                $subject = str_replace('{{'.$k.'}}', $v, $subject);
                $template = str_replace('{{'.$k.'}}', $v, $template);
            }

            $mail = Mage::getModel('core/email')
                ->setFromEmail($vendor->getEmail())
                ->setFromName($vendor->getVendorName())
                ->setToEmail($toEmail)
                ->setToName($toName)
                ->setSubject($subject)
                ->setBody($template)
                ->send();
            //mail('"'.$toName.'" <'.$toEmail.'>', $subject, $template, 'From: "'.$vendor->getVendorName().'" <'.$vendor->getEmail().'>');
        }

        Mage::helper('udropship')->addShipmentComment(
            $shipment,
            $this->__($vendor->getVendorName().': '.$comment)
        );
        $shipment->getCommentsCollection()->save();

        return $this;
    }

    /**
    * Collect renderers for different product types for admin item grids
    *
    * @param string|array $handle
    * @param string|Mage_Core_Block_Abstract $block
    * @param mixed $condition not used
    */
    public function applyItemRenderers($handle, $block, $condition=null, $conditionValue=true)
    {
        $layout = Mage::getModel('core/layout');
        $layout->getUpdate()->addHandle($handle)->load();
        $layout->generateXml();
        $renderers = $layout->getXpath("//action[@method='addItemRender']");

        if (is_string($block)) {
            $block = $layout->createBlock($block);
        }

        foreach ($renderers as $r) {
            if (is_null($condition) || preg_match($condition, (string)$r->block) == $conditionValue) {
                $block->addItemRender((string)$r->type, (string)$r->block, (string)$r->template);
            }
        }

        return $block;
    }

    /**
    * Get file name of label image for shipment tracking
    *
    * @todo make flexible enough for EPL
    * @param Mage_Sales_Model_Order_Shipment_Track $track
    * @return string
    */
    public function getTrackLabelFileName($track)
    {
        $shipment = $track->getShipment();
        return Mage::getConfig()->getVarDir('label').DS.$track->getNumber().'.png';
    }

    /**
    * In case customer object is missing in order object, retrieve
    *
    * @param Mage_Sales_Model_Order $order
    * @return Mage_Customer_Model_Customer
    */
    public function getOrderCustomer($order)
    {
        if (!$order->hasCustomer()) {
            $order->setCustomer(Mage::getModel('customer/customer')->load($order->getCustomerId()));
        }
        return $order->getCustomer();
    }

    /**
    * Get collection of order shipments for vendor interface
    *
    */
    public function getUsedMethodsByPoCollection($collection)
    {
        $allIds = $collection->getAllIds();
        $res  = Mage::getSingleton('core/resource');
        $read = $res->getConnection('core_read');
        if (!$this->isSalesFlat()) {
            $attr = Mage::getSingleton('eav/config')->getAttribute('shipment', 'udropship_method');
            return $read->fetchCol(
                $read->select()->distinct(true)
                    ->from($attr->getBackend()->getTable(), array('value'))
                    ->where('attribute_id=?', $attr->getId())
                    ->where('entity_id in (?)', $allIds)
            );
        } else {
            if ($collection instanceof Unirgy_DropshipPo_Model_Mysql4_Po_Collection) {
                return $read->fetchCol(
                    $read->select()->distinct(true)
                        ->from($res->getTableName('udpo/po'), array('udropship_method'))
                        ->where('entity_id in (?)', $allIds)
                );
            } else {
                return $read->fetchCol(
                    $read->select()->distinct(true)
                        ->from($res->getTableName('sales/shipment'), array('udropship_method'))
                        ->where('entity_id in (?)', $allIds)
                );
            }
        }
    }
    public function checkSku($sku = '')
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $query = "SELECT sku FROM catalog_product_entity where sku = ? ";
        $binds = array($sku);
        $oSku = $sku;
        $rs = $db->query($query, $binds);
        $i = 1;
        while(count($rs->fetchAll()))
        {
            $sku = $oSku.'-'.$i;
            $binds = array( $sku);
            $rs = $db->query($query, $binds);
            $i++;
        }
        return strtoupper($sku);

    }
    public function getVendorShipmentCollection()
    {
        if (!$this->_vendorShipmentCollection) {
            $vendorId = Mage::getSingleton('udropship/session')->getVendorId();
            $vendor = Mage::helper('udropship')->getVendor($vendorId);
            $collection = Mage::getModel('sales/order_shipment')->getCollection();
            $sqlMap = array();
            if (!$this->isSalesFlat()) {
                $collection
                    ->addAttributeToSelect(array('order_id', 'total_qty', 'udropship_status', 'udropship_method', 'udropship_method_description'))
                    ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id')
                    ->joinAttribute('order_created_at', 'order/created_at', 'order_id')
                    ->joinAttribute('shipping_method', 'order/shipping_method', 'order_id');
            } else {
                $orderTableQted = $collection->getResource()->getReadConnection()->quoteIdentifier('sales/order');
                $sqlMap['order_increment_id'] = "$orderTableQted.increment_id";
                $sqlMap['order_created_at']   = "$orderTableQted.created_at";
                $collection->join('sales/order', "$orderTableQted.entity_id=main_table.order_id", array(
                    'order_increment_id' => 'increment_id',
                    'order_created_at' => 'created_at',
                    'shipping_method',
                ));
                $collection->join('sales/order_item', "`sales/order_item`.order_id=main_table.order_id",
                                  array('delivery_date'));
            }

            $collection->addAttributeToFilter('main_table.udropship_vendor', $vendorId);


            $r = Mage::app()->getRequest();
            if (($v = $r->getParam('filter_order_id_from'))) {
                $collection->addAttributeToFilter($this->mapField('order_increment_id', $sqlMap), array('gteq'=>$v));
            }
            if (($v = $r->getParam('filter_order_id_to'))) {
                $collection->addAttributeToFilter($this->mapField('order_increment_id', $sqlMap), array('lteq'=>$v));
            }

            if (($v = $r->getParam('filter_order_date_from'))) {
                $_filterDate = Mage::app()->getLocale()->date();
                $_filterDate->set($v, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                $_filterDate->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
                $collection->addAttributeToFilter($this->mapField('order_created_at', $sqlMap), array('gteq'=>$_filterDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)));
            }
            if (($v = $r->getParam('filter_order_date_to'))) {
                $_filterDate = Mage::app()->getLocale()->date();
                $_filterDate->set($v, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                $_filterDate->addDay(1);
                $_filterDate->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
                $collection->addAttributeToFilter($this->mapField('order_created_at', $sqlMap), array('lteq'=>$_filterDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)));

            }

            if (($v = $r->getParam('filter_shipment_date_from'))) {
                $_filterDate = Mage::app()->getLocale()->date();
                $_filterDate->set($v, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                $_filterDate->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
                $collection->addAttributeToFilter('main_table.created_at', array('gteq'=>$_filterDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)));
            }
            if (($v = $r->getParam('filter_shipment_date_to'))) {
                $_filterDate = Mage::app()->getLocale()->date();
                $_filterDate->set($v, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                $_filterDate->addDay(1);
                $_filterDate->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
                $collection->addAttributeToFilter('main_table.created_at', array('lteq'=>$_filterDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)));
            }

            if (!$r->getParam('apply_filter') && $vendor->getData('vendor_po_grid_status_filter')) {
                $filterStatuses = $vendor->getData('vendor_po_grid_status_filter');
                $filterStatuses = array_combine($filterStatuses, array_fill(0, count($filterStatuses), 1));
                $r->setParam('filter_status', $filterStatuses);
            }

            if (!$this->isSalesFlat()) {
                if (($v = $r->getParam('filter_method'))) {
                    $collection->addAttributeToFilter('udropship_method', array('in'=>array_keys($v)));
                }
                if (($v = $r->getParam('filter_status'))) {
                    $collection->addAttributeToFilter('udropship_status', array('in'=>array_keys($v)));
                }
            } else {
                if (($v = $r->getParam('filter_method'))) {
                    $collection->addAttributeToFilter('main_table.udropship_method', array('in'=>array_keys($v)));
                }
                if (($v = $r->getParam('filter_status'))) {
                    $collection->addAttributeToFilter('main_table.udropship_status', array('in'=>array_keys($v)));
                }
            }

            if (!$r->getParam('sort_by') && $vendor->getData('vendor_po_grid_sortby')) {
                $r->setParam('sort_by', $vendor->getData('vendor_po_grid_sortby'));
                $r->setParam('sort_dir', $vendor->getData('vendor_po_grid_sortdir'));
            }

            // Set default sorting by delivery date
            $collection->getSelectSql()->order('sales/order_item.delivery_date DESC');
            $collection->getSelectSql()->order('main_table.order_id DESC');
            $collection->getSelectSql()->group('main_table.order_id');
            
            if (($v = $r->getParam('sort_by'))) {
                $map = array('order_date'=>'order_created_at', 'shipment_date'=>'created_at');
                if (isset($map[$v])) {
                    $v = $map[$v];
                }
                $collection->setOrder($v, $r->getParam('sort_dir'));
            }

            $this->_vendorShipmentCollection = $collection;
        }
        return $this->_vendorShipmentCollection;
    }

    public function mapField($field, $map)
    {
        return isset($map[$field]) ? $map[$field] : $field;
    }

    /**
    * Retrieve all shipping methods for carrier code
    *
    * Made for UPS which has CGI and XML methods
    *
    * @param string $carrierCode
    */
    public function getCarrierMethods($carrierCode, $allowedOnly=false)
    {
        if (empty($this->_carrierMethods[$allowedOnly][$carrierCode])) {
            $carrier = Mage::getSingleton('shipping/config')
                ->getCarrierInstance($carrierCode);
            $methods = array();
            if ($carrier) {
                if ($carrierCode=='ups') {
                    $upsMethods = Mage::getSingleton('udropship/source')
                        ->setPath('ups_shipping_method_combined')
                        ->toOptionHash();
                    $upsMethods = $upsMethods['UPS XML'] + $upsMethods['UPS CGI'];
                    if ($allowedOnly) {
                        $allowed = explode(',', $carrier->getConfigData('allowed_methods'));
                        $methods = array();
                        foreach ($allowed as $m) {
                            $methods['ups_'.$m] = $upsMethods[$m];
                        }
                    } else {
                        $methods = $upsMethods;
                    }
                } else {
                    if ($allowedOnly) {
                        $methods = $carrier->getAllowedMethods();
                    } else {
                        try {
                            $methods = $carrier->getCode('methods');
                        } catch (Exception $e) {
                            $methods = null;
                        }
                        if (!$methods) {
                            $methods = $carrier->getAllowedMethods();
                        }
                    }
                }
            }
            $this->_carrierMethods[$allowedOnly][$carrierCode] = $methods;
        }
        return $this->_carrierMethods[$allowedOnly][$carrierCode];
    }

    /**
    * Not used, for future use.
    *
    * @param mixed $allowedOnly
    */
    public function getAllCarriersMethods($allowedOnly=false)
    {
        $allCarrierMethods = array();
        $carrierNames = Mage::getSingleton('udropship/source')->getCarriers();
        foreach ($carrierNames as $code=>$carrier) {
            $allCarrierMethods[$code] = $this->getCarrierMethods($code, $allowedOnly);
        }
        return $allCarrierMethods;
    }

    public function getCarrierTitle($code)
    {
        $carrierNames = Mage::getSingleton('udropship/source')->getCarriers();
        return !empty($carrierNames[$code]) ? $carrierNames[$code] : Mage::helper('udropship')->__('Unknown');
    }

    /**
    * Region cache
    *
    * @param integer $regionId
    * @return Mage_Directory_Model_Region
    */
    public function getRegion($regionId)
    {
        if (!isset($this->_regions[$regionId])) {
            $this->_regions[$regionId] = Mage::getModel('directory/region')->load($regionId);
        }
        return $this->_regions[$regionId];
    }

    public function getCountry($countryId)
    {
        if (!isset($this->_countries[$countryId])) {
            $this->_countries[$countryId] = Mage::getModel('directory/country')->load($countryId);
        }
        return $this->_countries[$countryId];
    }

    /**
    * Get region code by region ID
    *
    * @param integer $regionId
    * @return string
    */
    public function getRegionCode($regionId)
    {
        return $this->getRegion($regionId)->getCode();
    }

    public function getCountryName($countryId)
    {
        return $this->getCountry($countryId)->getName();
    }

    public function getLabelCarrierInstance($carrierCode)
    {
        $carrierCode = strtolower($carrierCode);

        $labelConfig = Mage::getConfig()->getNode('global/udropship/labels/'.$carrierCode);
        if (!$labelConfig) {
            Mage::throwException('This carrier is not supported for label printing ('.$carrierCode.')');
        }

        $labelModel = Mage::getSingleton((string)$labelConfig->model);
        if (!$labelModel) {
            Mage::throwException('Invalid label model for this carrier ('.$carrierCode.')');
        }

        return $labelModel;
    }

    public function getLabelTypeInstance($labelType)
    {
        $labelType = strtolower($labelType);

        $labelConfig = Mage::getConfig()->getNode('global/udropship/label_types/'.$labelType);
        if (!$labelConfig) {
            Mage::throwException('This label type is not supported ('.$labelType.')');
        }

        $labelModel = Mage::getSingleton((string)$labelConfig->model);
        if (!$labelModel) {
            Mage::throwException('Invalid label model for this type ('.$labelType.')');
        }

        return $labelModel;
    }

    public function curlCall($url, $request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec ($ch);
#echo "<xmp>"; echo $response; exit;

        //check for error
        if (($error = curl_error($ch)))  {
            throw new Exception(Mage::helper('udropship')->__('Error connecting to API: %s', $error));
        }
        curl_close($ch);

        return $response;
    }

    public function sendDownload($fileName, $content, $contentType)
    {
        Mage::app()->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', strlen($content))
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setHeader('Last-Modified', date('r'))
            ->setBody($content)
            ->sendResponse();

        exit;
    }

    /**
    * Calculate total shipping price + handling fee
    *
    * For future use (doctab)
    *
    * @param float $cost
    * @param array $params
    */
    public function getShippingPriceWithHandlingFee($cost, array $params)
    {
        $numBoxes = !empty($params['num_boxes']) ? $params['num_boxes'] : 1;
        $handlingFee = $params['handling_fee'];
        $finalMethodPrice = 0;
        $handlingType = $params['handling_type'];
        if (!$handlingType) {
            $handlingType = Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_FIXED;
        }
        $handlingAction = $params['handling_action'];
        if (!$handlingAction) {
            $handlingAction = Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERORDER;
        }

        if($handlingAction == Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERPACKAGE)
        {
            if ($handlingType == Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT) {
                $finalMethodPrice = ($cost + ($cost * $handlingFee/100)) * $numBoxes;
            } else {
                $finalMethodPrice = ($cost + $handlingFee) * $numBoxes;
            }
        } else {
            if ($handlingType == Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT) {
                $finalMethodPrice = ($cost * $numBoxes) + ($cost * $numBoxes * $handlingFee/100);
            } else {
                $finalMethodPrice = ($cost * $numBoxes) + $handlingFee;
            }

        }
        return $finalMethodPrice;
    }

    public function usortByPosition($a, $b)
    {
        return $a['position']<$b['position'] ? -1 : ($a['position']>$b['position'] ? 1 : 0);
    }

    /**
    * vsprintf extended to use associated array key names
    *
    * @link http://us.php.net/manual/en/function.vsprintf.php#87031
    * @param string $format
    * @param array $data
    */
    public function vnsprintf($format, array $data)
    {
        preg_match_all('/ (?<!%) % ( (?: [[:alpha:]_-][[:alnum:]_-]* | ([-+])? [0-9]+ (?(2) (?:\.[0-9]+)? | \.[0-9]+ ) ) ) \$ [-+]? \'? .? -? [0-9]* (\.[0-9]+)? \w/x', $format, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $offset = 0;
        $keys = array_keys($data);
        foreach ($match as &$value) {
            if (($key = array_search( $value[1][0], $keys, TRUE) ) !== FALSE
                || ( is_numeric( $value[1][0] )
                    && ( $key = array_search( (int)$value[1][0], $keys, TRUE) ) !== FALSE)
            ) {
                $len = strlen( $value[1][0]);
                $format = substr_replace( $format, 1 + $key, $offset + $value[1][1], $len);
                $offset -= $len - strlen( 1 + $key);
            }
        }
        return vsprintf($format, $data);
    }

    protected $_storeOrigin = array();
    /**
    * For potential future use
    *
    * @param mixed $store
    * @param Unirgy_Dropship_Model_Vendor $object
    */
    protected function _setOriginAddress($store, $object=null)
    {
        if (!Mage::getStoreConfig('udropship/vendor/tax_by_vendor', $store)) {
            return;
        }
        $origin = null;
        $store = Mage::app()->getStore($store);
        $sId = $store->getId();
        if (is_null($object)) {
            if (!empty($this->_storeOrigin[$sId])) {
                $origin = $this->_storeOrigin[$sId];
                $this->_storeOrigin[$sId] = array();
            }
        } else {
            if (empty($this->_storeOrigin[$sId])) {
                $this->_storeOrigin[$sId] = Mage::getStoreConfig('shipping/origin', $store);
            }
            if ($object instanceof Mage_Sales_Model_Quote_Item || $object instanceof Mage_Sales_Model_Quote_Address_Item) {
                $object = $object->getProduct();
            }
            if ($object instanceof Mage_Catalog_Model_Product || is_numeric($object)) {
                $object = $this->getVendor($object);
            }
            $origin = array(
                'country_id' => $object->getCountryId(),
                'region_id' => $object->getRegionId(),
                'postcode' => $object->getZip(),
            );
        }
        if ($origin) {
            $root = Mage::getConfig()->getNode("stores/{$store->getCode()}/shipping/origin");
            foreach (array('country_id', 'region_id', 'postcode') as $v) {
                $root->$v = $origin[$v];
            }
        }
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

    public function addAdminhtmlVersion($module='Unirgy_Dropship')
    {
        $layout = Mage::app()->getLayout();
        $version = (string)Mage::getConfig()->getNode("modules/{$module}/version");

        $layout->getBlock('before_body_end')->append($layout->createBlock('core/text')->setText('
            <script type="text/javascript">$$(".legality")[0].insert({after:"'.$module.' ver. '.$version.', "});</script>
        '));

        return $this;
    }


    public function addTo($obj, $key, $value)
    {
        $new = $obj->getData($key)+$value;
        $obj->setData($key, $new);
        return $new;
    }

    protected $_queue = array();
    public function resetQueue()
    {
        $this->_queue = array();
    }

    public function addToQueue($action)
    {
        $this->_queue[] = $action;
        return $this;
    }

    public function processQueue()
    {
        $transport = null;

        if (Mage::getStoreConfig('udropship/misc/mail_transport')=='sendmail') {
            $sendmail = true;
            $transport = new Zend_Mail_Transport_Sendmail();
        }
        // Integrate with Aschroder_SMTPPro
        elseif ($this->isModuleActive('Aschroder_SMTPPro')) {
            $smtppro = Mage::helper('smtppro');
            $transport = $smtppro->getSMTPProTransport();
        }
        // Integrate with Aschroder_GoogleAppsEmail
        elseif ($this->isModuleActive('Aschroder_GoogleAppsEmail')) {
            $googleappsemail = Mage::helper('googleappsemail');
            $transport = $googleappsemail->getGoogleAppsEmailTransport();
        }
        // integrate with ArtsOnIT_AdvancedSmtp
        elseif ($this->isMageAdvancedsmtpActive()) {
            $advsmtp = Mage::helper('advancedsmtp');
            $transport = $advsmtp->getTransport();
        }

        foreach ($this->_queue as $action) {
            if ($action instanceof Zend_Mail) {
                /* @var $action Zend_Mail */
                if (!empty($smtppro) && $smtppro->isReplyToStoreEmail()) {
                    if(method_exists($action, 'setReplyTo')) {
                        $action->setReplyTo($action->getFrom());
                    }else {
                        $action->addHeader('Reply-To', $action->getFrom());
                    }
                }
                if (!empty($sendmail)) {
                    $transport->parameters = '-f'.$action->getFrom();
                }
                $action->send($transport);
            } elseif (is_array($action)) { //array($object, $method, $args)
                call_user_func_array(array($action[0], $action[1]), !empty($action[2]) ? $action[2] : array());
            }
        }
        $this->resetQueue();
        return $this;
    }

    public function isMageAdvancedsmtpActive()
    {
        return $this->isModuleActive('Mage_Advancedsmtp') && Mage::getStoreConfig('advancedsmtp/settings/enabled');
    }

    public function getNewVendors($days = 30)
    {
        $vendors = Mage::getModel('udropship/vendor')->getCollection()
            ->addFieldToFilter('created_at', array('gt'=>date('Y-m-d', time()-$days*86400)))
            ->addOrder('created_at', 'desc');
        return $vendors;
    }

    public function loadCustomData($obj)
    {
        // add custom vars
        if ($obj->getCustomVarsCombined()) {
            $varsCombined = $obj->getCustomVarsCombined();
            if (strpos($varsCombined, 'a:')===0) {
                $vars = @unserialize($varsCombined);
            } elseif (strpos($varsCombined, '{')===0) {
                $vars = Zend_Json::decode($varsCombined);
            }
            if (!empty($vars)) {
                $obj->addData($vars);
            }
        }

        // add custom data
        if (($customData = $obj->getData('custom_data_combined'))) {
            $arr = preg_split('#={5}\s+([^=]+)\s+={5}#', $customData, -1, PREG_SPLIT_DELIM_CAPTURE);
            $data = array();
            for ($i=1, $l=sizeof($arr); $i<$l; $i+=2) {
                $obj->setData(trim($arr[$i]), trim($arr[$i+1]));
            }
        }

        // add custom vars defaults
        foreach (Mage::getConfig()->getNode('global/udropship/vendor/fields')->children() as $code=>$node) {
            $_key = $node->name ? (string)$node->name : $code;
            if ((string)$node->type=='disabled') {
                continue;
            }
            if (((string)$node->type=='image' || (string)$node->type=='file')
                && $obj->hasData($_key) && is_array($obj->getData($_key)))
            {
                $arr = $obj->getData($_key);
                $obj->setData($_key, $arr['value']);
            }
            if ($node->default && !$obj->hasData($_key)) {
                if ($node->type == 'multiselect') {
                    $defVals = explode(',', (string)$node->default);
                    $obj->setData($_key, $defVals);
                } else {
                    $obj->setData($_key, (string)$node->default);
                }
            }
        }

        return $this;
    }

    public function processPostMultiselects(&$data)
    {
        $fields = Mage::getConfig()->getNode('global/udropship/vendor/fields')->children();

        $visible = Mage::getStoreConfig('udropship/vendor/visible_preferences');
        $visible = $visible ? explode(',', $visible) : array();

        $isAdmin = Mage::app()->getStore()->isAdmin();

        foreach ($fields as $code=>$node) {
            if ((string)$node->type=='multiselect' && empty($data[$code]) && ($isAdmin || empty($visible) || in_array($code, $visible))) {
                $data[$code] = array();
            }
        }

        return $this;
    }

    public function processCustomVars($obj)
    {
        $customVars = array();
        
        $additionalVars = array('google_reference',
                                            'vendor_website',
                                            'has_same_day_delivery',
                                            'florist_type',
                                            'about_owner',
                                            'owner_photo',
                                            'dozen_rose_price',
                                            'open_at',
                                            'close_at',
                                            'wopen_at',
                                            'wclose_at',
                                            'v_timezone');
        // Additioncal custom data
        foreach($additionalVars as $adVar) {
            if($obj->hasData($adVar) && '' != $obj->getData($adVar)) {
                $customVars[$adVar] = $obj->getData($adVar);
            }
        }

        $visible = Mage::getStoreConfig('udropship/vendor/visible_preferences');
        $visible = $visible ? explode(',', $visible) : array();

        foreach (Mage::getConfig()->getNode('global/udropship/vendor/fields')->children() as $code=>$node) {
            $_key = $node->name ? (string)$node->name : $code;
            switch ((string)$node->type) {
            case 'disabled':
                continue;

            case  'image': case 'file':
                if ($obj->hasData($_key) && is_array($obj->getData($_key))) {
                    $arr = $obj->getData($_key);
                    if (!empty($arr['delete'])) {
                        @unlink(Mage::getConfig()->getBaseDir('media').DS.'vendor'.strtr($arr['value'], '/', DS));
                        $obj->unsetData($_key);
                    } else {
                        $obj->setData($_key, $arr['value']);
                    }
                }
                break;

            case 'multiselect':
                if ($obj->hasData($_key) && !is_array($obj->getData($_key))) {
                    $obj->setData($_key, (array)$obj->getData($_key));
                }
                break;
            }
            if ($obj->hasData($_key)) {
                $customVars[$_key] = $obj->getData($_key);
                $customVars[$code] = $obj->getData($_key);
            }
        }
        $obj->setCustomVarsCombined(Zend_Json::encode($customVars));

        return $this;
    }

    public function addMessageOnce($message, $module='checkout', $method='addError')
    {
        $session = Mage::getSingleton($module.'/session');
        $found = false;
#if (!Mage::app()->getRequest()->isPost()) print_r($message);
        foreach ($session->getMessages(false) as $m) {
#if (!Mage::app()->getRequest()->isPost()) print_r($m);
            if ($m->getCode() == $message) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $session->$method($message);
        }
        return $this;
    }

    public function getNextWorkDayTime($date=null)
    {
        $time = is_string($date) ? strtotime($date) : (is_int($date) ? $date : time());
        $y = date('Y', $time);
        // calculate federal holidays
        $holidays = array();
        // month/day (jan 1st). iteration/wday/month (3rd monday in january)
        $hdata = array('1/1'/*newyr*/, '7/4'/*jul4*/, '11/11'/*vet*/, '12/25'/*xmas*/, '3/1/1'/*mlk*/, '3/1/2'/*pres*/, '5/1/5'/*memo*/, '1/1/9'/*labor*/, '2/1/10'/*col*/, '4/4/11'/*thanks*/);
        foreach ($hdata as $h1) {
            $h = explode('/', $h1);
            if (sizeof($h)==2) { // by date
                $htime = mktime(0, 0, 0, $h[0], $h[1], $y); // time of holiday
                $w = date('w', $htime); // get weekday of holiday
                $htime += $w==0 ? 86400 : ($w==6 ? -86400 : 0); // if weekend, adjust
            } else { // by weekday
                $htime = mktime(0, 0, 0, $h[2], 1, $y); // get 1st day of month
                $w = date('w', $htime); // weekday of first day of month
                $d = 1+($h[1]-$w+7)%7; // get to the 1st weekday
                for ($t=$htime, $i=1; $i<=$h[0]; $i++, $d+=7) { // iterate to nth weekday
                     $t = mktime(0, 0, 0, $h[2], $d, $y); // get next weekday
                     if (date('n', $t)>$h[2]) break; // check that it's still in the same month
                     $htime = $t; // valid
                }
            }
            $holidays[] = $htime; // save the holiday
        }
        for ($i=0; $i<5; $i++, $time+=86400) { // 5 days should be enough to get to workday
            if (in_array(date('w', $time), array(0, 6))) continue; // skip weekends
            foreach ($holidays as $h) { // iterate through holidays
                if ($time>=$h && $time<=$h+86400) continue 2; // skip holidays
            }
            break; // found the workday
        }
        return $time;
    }

    /**
     * Poll carriers tracking API
     *
     * @param mixed $tracks
     */
    public function collectTracking($tracks)
    {
        $requests = array();
        foreach ($tracks as $track) {
            $cCode = $track->getCarrierCode();
            if (!$cCode) {
                continue;
            }
            $vId = $track->getShipment()->getUdropshipVendor();
            $v = Mage::helper('udropship')->getVendor($vId);
            if (!$v->getTrackApi($cCode)) {
                continue;
            }
            $requests[$cCode][$vId][$track->getNumber()][] = $track;
        }
        foreach ($requests as $cCode=>$vendors) {
            foreach ($vendors as $vId=>$trackIds) {
                $v = Mage::helper('udropship')->getVendor($vId);
                try {
                    $result = $v->getTrackApi($cCode)->collectTracking($v, array_keys($trackIds));
                } catch (Exception $e) {
                    $this->_processPollTrackingFailed($trackIds, $e);
                    continue;
                }
#print_r($result); echo "\n";
                $processTracks = array();
                foreach ($result as $trackId=>$status) {
                    foreach ($trackIds[$trackId] as $track) {
                        if ($status==Unirgy_Dropship_Model_Source::TRACK_STATUS_PENDING) {
                            $repeatIn = Mage::getStoreConfig('udropship/customer/repeat_poll_tracking', $track->getShipment()->getOrder()->getStoreId());
                            if ($repeatIn<=0) {
                                $repeatIn = 12;
                            }
                            $repeatIn = $repeatIn*60*60;
                            $track->setNextCheck(date('Y-m-d H:i:s', time()+$repeatIn))->save();
                            continue;
                        }

                        $track->setUdropshipStatus($status);
                        switch ($status) {
                        case Unirgy_Dropship_Model_Source::TRACK_STATUS_READY:
                            Mage::helper('udropship')->addShipmentComment(
                                $track->getShipment(),
                                $this->__('Tracking ID %s was picked up from %s', $trackId, $v->getVendorName())
                            );
                            $track->getShipment()->save();
                            break;

                        case Unirgy_Dropship_Model_Source::TRACK_STATUS_DELIVERED:
                            Mage::helper('udropship')->addShipmentComment(
                                $track->getShipment(),
                                $this->__('Tracking ID %s was delivered to customer', $trackId)
                            );
                            $track->getShipment()->save();
                            break;
                        }
                        if (empty($processTracks[$track->getParentId()])) {
                            $processTracks[$track->getParentId()] = array();
                        }
                        $processTracks[$track->getParentId()][] = $track;
                    }
                }
                foreach ($processTracks as $_pTracks) {
                    try {
                        $this->processTrackStatus($_pTracks, true);
                    } catch (Exception $e) {
                        $this->_processPollTrackingFailed($_pTracks, $e);
                        continue;
                    }
                }
            }
        }
    }

    protected function _processPollTrackingFailed($tracks, Exception $e)
    {
        $tracksByStore = array();
        foreach ($tracks as $_track) {
            if (is_array($_track)) {
                foreach ($_track as $__track) {
                    $tracksByStore[$__track->getShipment()->getOrder()->getStoreId()][] = $__track;
                }
            } elseif ($_track instanceof Mage_Sales_Model_Order_Shipment_Track) {
                $tracksByStore[$_track->getShipment()->getOrder()->getStoreId()][] = $_track;
            }
        }
        foreach ($tracksByStore as $_sId => $_tracks) {
            Mage::helper('udropship/error')->sendPollTrackingFailedNotification($_tracks, "$e", $_sId);
        }
        return $this;
    }

    /**
     * Sending email with Invoice data
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendTrackingNotificationEmail($track, $comment='')
    {
        if (is_array($track)) {
            $tracks = $track;
            reset($tracks);
            $track = current($track);
        } else {
            $tracks = array($track);
        }
        $shipment = $track->getShipment();
        $order = $shipment->getOrder();
        $storeId = $order->getStoreId();

        if (!Mage::helper('sales')->canSendNewShipmentEmail($storeId)) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $storeId),
            'store' => $storeId
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $copyTo = Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_COPY_TO, $storeId);
        if (!empty($copyTo)) {
            $copyTo = explode(',', $copyTo);
        }
        $copyMethod = Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig('udropship/customer/tracking_email_template_guest', $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig('udropship/customer/tracking_email_template', $storeId);
            $customerName = $order->getCustomerName();
        }

        $sendTo[] = array(
            'name'  => $customerName,
            'email' => $order->getCustomerEmail()
        );
        if ($copyTo && $copyMethod == 'bcc') {
            foreach ($copyTo as $email) {
                $mailTemplate->addBcc($email);
            }
        }

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_IDENTITY, $storeId),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'       => $order,
                        'shipment'    => $shipment,
                        'track'       => $track,
                        'tracks'      => $tracks,
                        'comment'     => $comment,
                        'billing'     => $order->getBillingAddress(),
                        'payment_html'=> $paymentBlock->toHtml(),
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

    protected function _processTrackStatusSave($save, $object)
    {
        if ($save===true) {
            $object->save();
        } elseif ($save instanceof Mage_Core_Model_Resource_Transaction) {
            $save->addObject($object);
        }
    }

    /**
     * Process tracking status update
     *
     * Will process only tracks with TRACK_STATUS_READY status
     *
     * @param Mage_Sales_Model_Order_Shipment_Track $track
     * @param boolean|Mage_Core_Model_Resource_Transaction $save
     * @param null|boolean $complete
     */
    public function processTrackStatus($track, $save=false, $complete=null)
    {
        if (is_array($track)) {
            $tracks = $track;
            reset($tracks);
            $track = current($track);
        } else {
            $tracks = array($track);
        }
        $shipment = $track->getShipment();

        $order = $shipment->getOrder();
        $storeId = $order->getStoreId();

        $saveShipment = false;
        $saveOrder = false; //not used yet

        $notifyTracks = array();

        foreach ($tracks as $track) {
            $saveTrack = false;

            // is the track ready to be marked as shipped
            $trackReady = $track->getUdropshipStatus()===Unirgy_Dropship_Model_Source::TRACK_STATUS_READY;
            // is the track shipped
            $shipped = $track->getUdropshipStatus()==Unirgy_Dropship_Model_Source::TRACK_STATUS_SHIPPED;
            // is the track delivered
            $delivered = $track->getUdropshipStatus()===Unirgy_Dropship_Model_Source::TRACK_STATUS_DELIVERED;

            // actions that need to be done if the track is not marked as shipped yet
            if (!$shipped) {
                // if new track record, set initial values
                if (!$track->getUdropshipStatus()) {
                    $vendorId = $shipment->getUdropshipVendor();
                    $pollTracking = Mage::getStoreConfig('udropship/customer/poll_tracking', $storeId);
                    $trackApi = Mage::helper('udropship')->getVendor($vendorId)->getTrackApi();
                    if ($pollTracking && $trackApi) {
                        $track->setUdropshipStatus(Unirgy_Dropship_Model_Source::TRACK_STATUS_PENDING);
                        $repeatIn = Mage::getStoreConfig('udropship/customer/repeat_poll_tracking', $track->getShipment()->getOrder()->getStoreId());
                        if ($repeatIn<=0) {
                            $repeatIn = 12;
                        }
                        $repeatIn = $repeatIn*60*60;
                        $track->setNextCheck(date('Y-m-d H:i:s', time()+$repeatIn));
                    } else {
                        $track->setUdropshipStatus(Unirgy_Dropship_Model_Source::TRACK_STATUS_READY);
                    }
                    $saveTrack = true;
                }
                if ($track->getUdropshipStatus()==Unirgy_Dropship_Model_Source::TRACK_STATUS_READY) {
                    $track->setUdropshipStatus(Unirgy_Dropship_Model_Source::TRACK_STATUS_SHIPPED);
                    $notifyTracks[] = $track;
                    $saveTrack = true;
                }
                if ($saveTrack) {
                    $this->_processTrackStatusSave($save, $track);
                }
            }
        }

        if (!empty($notifyTracks)) {
            $notifyOnOld = Mage::getStoreConfig('udropship/customer/notify_on', $storeId);
            $notifyOn = Mage::getStoreConfig('udropship/customer/notify_on_tracking', $storeId);
            if ($notifyOn) {
                $this->sendTrackingNotificationEmail($notifyTracks);
                $shipment->setEmailSent(true);
                $saveShipment = true;
            } elseif ($notifyOnOld==Unirgy_Dropship_Model_Source::NOTIFYON_TRACK) {
                $shipment->sendEmail();
                $shipment->setEmailSent(true);
                $saveShipment = true;
            }
        }

        if ($shipment->getUdropshipStatus()==Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_SHIPPED || $shipment->getUdropshipStatus()==Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED) {
            return;
        }

        if (is_null($complete)) {
            if (Mage::getStoreConfigFlag('udropship/vendor/auto_shipment_complete', $storeId)) {
                switch (Mage::getStoreConfigFlag('udropship/vendor/auto_shipment_complete', $storeId)) {
                    case Unirgy_Dropship_Model_Source::AUTO_SHIPMENT_COMPLETE_ANY:
                        $pickedUpTracks = Mage::getModel('sales/order_shipment_track')->getCollection()
                            ->setShipmentFilter($shipment->getId())
                            ->addAttributeToFilter('udropship_status', array('in'=>array(Unirgy_Dropship_Model_Source::TRACK_STATUS_SHIPPED, Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED)))
                        ;
                        $complete = $pickedUpTracks->count()>0;
                        break;
                    default:
                        $pendingTracks = Mage::getModel('sales/order_shipment_track')->getCollection()
                            ->setShipmentFilter($shipment->getId())
                            ->addAttributeToFilter('udropship_status', array('nin'=>array(Unirgy_Dropship_Model_Source::TRACK_STATUS_SHIPPED, Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED)))
                        ;
                        $complete = !$pendingTracks->count();
                        break;
                }
            } else {
                $complete = false;
            }
        }

        if ($complete) {
            $this->completeShipment($shipment, $save, $delivered);
            $saveShipment = true;
        } elseif ($shipment->getUdropshipStatus()!=Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_PARTIAL) {

            $shipment->setUdropshipStatus(Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_PARTIAL);
            $saveShipment = true;
        }
        if ($saveShipment) {
            foreach ($shipment->getAllTracks() as $t) {
                foreach ($tracks as $_t) {
                    if ($t->getEntityId()==$_t->getEntityId()) {
                        $t->setData($_t->getData());
                        break;
                    }
                }
            }
            $this->_processTrackStatusSave($save, $shipment);
        }

        if ($complete) {
            $this->completeUdpoIfShipped($shipment, $save);
            $this->completeOrderIfShipped($shipment, $save);
        }

        return $this;
    }

    public function registerShipmentItem($item, $save)
    {
        if ($this->isUdpoActive()) {
            Mage::helper('udpo')->completeShipmentItem($item, $save);
        } else {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy(true)) {
                $item->setQty(1);
            }
            if ($item->getQty()>0) {
                $item->register();
                $this->_processTrackStatusSave($save, $orderItem);
            }
        }
    }

    public function completeShipment($shipment, $save=false, $delivered=false)
    {
        $order = $shipment->getOrder();
        $storeId = $order->getStoreId();

        $newStatus = $delivered
            ? Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED
            : Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_SHIPPED;

        if ($newStatus == $shipment->getUdropshipStatus()) {
            return $this;
        }
        $shipment->setUdropshipStatus($newStatus);
        Mage::helper('udropship')->addShipmentComment(
            $shipment,
            $this->__('Shipment has been complete')
        );

        foreach ($shipment->getAllItems() as $item) {
            $this->registerShipmentItem($item, $save);
        }

        $notifyOnOld = Mage::getStoreConfig('udropship/customer/notify_on', $storeId);
        $notifyOn = Mage::getStoreConfig('udropship/customer/notify_on_shipment', $storeId);
        if (($notifyOn || $notifyOnOld==Unirgy_Dropship_Model_Source::NOTIFYON_SHIPMENT) && !$delivered) {
            $shipment->sendEmail();
            $shipment->setEmailSent(true);
        }

        $this->_processTrackStatusSave($save, $shipment);

        if ($this->isUdpoActive()) {
            Mage::helper('udpo')->completeShipment($shipment, $save);
        }

        return $this;
    }

    public function completeUdpoIfShipped($shipment, $save=false, $force=true)
    {
        if ($this->isUdpoActive()) {
            Mage::helper('udpo')->completeUdpoIfShipped($shipment, $save, $force);
        }
    }

    public function completeOrderIfShipped($shipment, $save=false, $force=true)
    {
        $order = $shipment->getOrder();

        $pendingShipments = Mage::getModel('sales/order_shipment')->getCollection()
            ->setOrderFilter($order->getId())
            ->addAttributeToFilter('entity_id', array('neq'=>$shipment->getId()))
            ->addAttributeToFilter('udropship_status', array('nin'=>array(Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_SHIPPED, Unirgy_Dropship_Model_Source::SHIPMENT_STATUS_DELIVERED)))
        ;

        if (!$pendingShipments->count() && $force) {
            // will not work with 1.4.x
            #$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true);
        }
        $order->setIsInProcess(true);
        $this->_processTrackStatusSave($save, $order);

        return $this;
    }

    public function getVendorSku($item)
    {
        if ($this->isModuleActive('udmulti') && Mage::helper('udmulti')->isActive()) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }
            return Mage::helper('udmulti')->getVendorSku($item->getProductId(), $item->getUdropshipVendor(), $item->getSku());
        } else {
            return $item->getSku();
        }
        return $sku;
    }

    protected $_shippingMethods;
    public function getShippingMethods()
    {
        if (!$this->_shippingMethods) {
            $this->_shippingMethods = Mage::getModel('udropship/shipping')->getCollection();
        }
        return $this->_shippingMethods;
    }

    protected $_systemShippingMethods;
    public function getSystemShippingMethods()
    {
        if (!$this->_systemShippingMethods) {
            $systemMethods = array();
            $shipping = $this->getShippingMethods();
            foreach ($shipping as $s) {
                if (!$s->getSystemMethods()) {
                    continue;
                }
                foreach ($s->getSystemMethods() as $c=>$m) {
                    $systemMethods[$c][$m] = $s;
                }
            }
            $this->_systemShippingMethods = $systemMethods;
        }
        return $this->_systemShippingMethods;
    }

    protected $_multiSystemShippingMethods;
    public function getMultiSystemShippingMethods()
    {
        if (!$this->_systemShippingMethods) {
            $systemMethods = array();
            $shipping = $this->getShippingMethods();
            foreach ($shipping as $s) {
                if (!$s->getSystemMethods()) {
                    continue;
                }
                foreach ($s->getSystemMethods() as $c=>$m) {
                    if (empty($systemMethods[$c][$m])) {
                        $systemMethods[$c][$m] = array();
                    }
                    $systemMethods[$c][$m][] = $s;
                }
            }
            $this->_multiSystemShippingMethods = $systemMethods;
        }
        return $this->_multiSystemShippingMethods;
    }

    public function saveThisVendorProducts($data, $v)
    {
        return $this->_saveVendorProducts($data, $v);
    }
    public function saveVendorProducts($data)
    {
        return $this->_saveVendorProducts($data, Mage::getSingleton('udropship/session')->getVendor());
    }
    protected function _saveVendorProducts($data, $v)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        //Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        $this->_oldStoreId = Mage::app()->getStore()->getId();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('cost')
            ->addIdFilter(array_keys($data));

        if ($v->getId()==Mage::getStoreConfig('udropship/vendor/local_vendor')) {
            $attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'udropship_vendor');
            $products->getSelect()->joinLeft(
                array('_udv'=>$attr->getBackend()->getTable()),
                '_udv.entity_id=e.entity_id and _udv.store_id=0 and _udv.attribute_id='.$attr->getId().' and _udv.value='.$v->getId(),
                array('udropship_vendor'=>'value')
            );
        } else {
            $products->addAttributeToFilter('udropship_vendor', $v->getId());
        }

        $products->load();
        Mage::app()->setCurrentStore($this->_oldStoreId);

        if (!$products) {
            return false;
        }
        Mage::getModel('cataloginventory/stock')->addItemsToProducts($products);

        $cnt = 0;
        foreach ($products as $p) {
            if (empty($data[$p->getId()])) {
                continue;
            }
            $d = $data[$p->getId()];
            $updateProduct = false;
            $updateStock = false;
            /*
            if ($p->hasCost() && empty($d['vendor_cost'])) {
                $p->unsCost();
                $updateProduct = true;
            } elseif (!empty($d['vendor_cost']) && $d['vendor_cost']!=$p->getCost()) {
                $p->setCost($d['vendor_cost']);
                $updateProduct = true;
            }
            */
            $ps = $p->getStockItem();
            if (isset($d['stock_status']) && $d['stock_status']!=$ps->getIsInStock()) {
                $ps->setIsInStock($d['stock_status']);
                $updateStock = true;
            }
            if (isset($d['stock_qty']) && $d['stock_qty']!=$ps->getQty()) {
                $ps->setQty($d['stock_qty']);
                $updateStock = true;
            } elseif (!isset($d['stock_qty']) && isset($d['stock_qty_add'])) {
                $ps->setQty($ps->getQty()+$d['stock_qty_add']);
                $updateStock = true;
            }
            if ($updateProduct) {
                $p->save();
            }
            if ($updateStock) {
                $ps->save();
            }
            if ($updateProduct || $updateStock) {
                $cnt++;
            }
        }
        return $cnt;
    }

    public function compareMageVer($ceVer, $eeVer=null, $op='>=')
    {
        $eeVer = is_null($eeVer) ? $ceVer : $eeVer;
        return $this->isModuleActive('Enterprise_Enterprise')
            ? version_compare(Mage::getVersion(), $eeVer, $op)
            : version_compare(Mage::getVersion(), $ceVer, $op);
    }

    protected $_hasMageFeature = array();
    public function hasMageFeature($feature)
    {
        if (!isset($this->_hasMageFeature[$feature])) {
            $flag = false;
            switch ($feature) {
            case 'fedex.soap':
                $flag = $this->compareMageVer('1.6.0.0', '1.11.0', '>=');
                break;
            case 'order_item.base_cost':
                $flag = $this->compareMageVer('1.4.0.1', '1.8.0', '>=');
                break;

            case 'sales_flat':
                $flag = $this->compareMageVer('1.4.1.0', '1.8.0', '>=');
                break;

            case 'wysiwyg_allowed':
                $flag = Mage::getStoreConfig('udropship/vendor/allow_wysiwyg') && $this->compareMageVer('1.4.0');
                break;

            case 'stock_can_subtract_qty':
                $flag = $this->compareMageVer('1.4.1.1', '1.9.0', '>=');
                break;

            case 'indexer_1.4':
            case 'table.product_relation':
            case 'table.eav_attribute_label':
            case 'table.catalog_eav_attribute':
            case 'attr.is_wysiwyg_enabled':
                $flag = $this->compareMageVer('1.4', '1.6');
                break;
            case 'track_number':
                $flag = $this->compareMageVer('1.6', '1.11');
                break;
            }
            $this->_hasMageFeature[$feature] = $flag;
        }
        return $this->_hasMageFeature[$feature];
    }

    public function trackNumberField()
    {
        return $this->hasMageFeature('track_number') ? 'track_number' : 'number';
    }

    public function isSalesFlat()
    {
        return $this->hasMageFeature('sales_flat');
    }

    public function isWysiwygAllowed()
    {
        return $this->hasMageFeature('wysiwyg_allowed');
    }

    public function assignVendorSkus($po)
    {
        $storeId = $po->getStoreId();
        $attr = Mage::getStoreConfig('udropship/vendor/vendor_sku_attribute', $storeId);
        Mage::helper('udropship')->addVendorSkus($po);
        foreach ($po->getAllItems() as $item) {
            $oItem = $item->getOrderItem();
            $oItemParent = $oItem->getParentItem();
            if ($item->getVendorSku()) {
                $item->setData('__orig_sku', $item->getSku());
                $item->setSku($item->getVendorSku());
                if ($oItem->getProductType() == 'bundle' || ($oItemParent && $oItemParent->getProductType() == 'bundle')) {
                    $oItem->setData('__orig_sku', $oItem->getSku());
                    $oItem->setSku($item->getVendorSku());
                }
            }
            $pOpts = $item->getOrderItem()->getProductOptions();
            if (!empty($pOpts['simple_sku']) && $item->getVendorSimpleSku()) {
                $item->setData('__orig_simple_sku', $pOpts['simple_sku']);
                $pOpts['simple_sku'] = $item->getVendorSimpleSku();
            }
            $item->getOrderItem()->setProductOptions($pOpts);
        }
        if ($po instanceof Mage_Sales_Model_Order_Shipment) {
            Mage::dispatchEvent('udropship_shipment_assign_vendor_skus', array('shipment'=>$po, 'attribute_code'=>$attr));
        } elseif ($po instanceof Unirgy_DropshipPo_Model_Po) {
            Mage::dispatchEvent('udpo_po_assign_vendor_skus', array('udpo'=>$po, 'attribute_code'=>$attr));
        }
        return $this;
    }

    public function unassignVendorSkus($po)
    {
        $storeId = $po->getStoreId();
        $attr = Mage::getStoreConfig('udropship/vendor/vendor_sku_attribute', $storeId);
        //if ($attr && $attr!='sku') {
            foreach ($po->getAllItems() as $item) {
                $oItem = $item->getOrderItem();
                $oItemParent = $oItem->getParentItem();
                if ($item->hasData('__orig_sku')) {
                    $item->setSku($item->getData('__orig_sku'));
                    if ($oItem->getProductType() == 'bundle' || ($oItemParent && $oItemParent->getProductType() == 'bundle')) {
                        $oItem->setSku($oItem->getData('__orig_sku'));
                    }
                }
                $pOpts = $item->getOrderItem()->getProductOptions();
                if ($item->hasData('__orig_simple_sku')) {
                    $pOpts['simple_sku'] = $item->getData('__orig_simple_sku');
                }
                $item->getOrderItem()->setProductOptions($pOpts);
            }
        //}
        if ($po instanceof Mage_Sales_Model_Order_Shipment) {
            Mage::dispatchEvent('udropship_shipment_unassign_vendor_skus', array('udpo'=>$po, 'attribute_code'=>$attr));
        } elseif ($po instanceof Unirgy_DropshipPo_Model_Po) {
            Mage::dispatchEvent('udpo_po_unassign_vendor_skus', array('udpo'=>$po, 'attribute_code'=>$attr));
        }
        return $this;
    }

    public function addVendorSkus($po)
    {
        $storeId = $po->getStoreId();
        $attr = Mage::getStoreConfig('udropship/vendor/vendor_sku_attribute', $storeId);
        $productIds = array();
        $simpleSkus = array();
        foreach ($po->getAllItems() as $item) {
            if (null === $item->getData('vendor_sku')) {
                $item->setFirstAddVendorSkuFlag(true);
                $productIds[] = $item->getProductId();
            }
            if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
                if (null === $item->getData('vendor_simple_sku')) {
                    $item->setFirstAddVendorSimpleSkuFlag(true);
                    $simpleSkus[spl_object_hash($item)] = $item->getOrderItem()->getProductOptionByCode('simple_sku');
                }
            }
        }
        if ($attr && $attr!='sku') {
            $attrFilters = array();
            if (!empty($productIds)) {
                $attrFilters[] = array('attribute' => 'entity_id', 'in' => array_values($productIds));
            }
            if (!empty($simpleSkus)) {
                $attrFilters[] = array('attribute' => 'sku', 'in' => array_values($simpleSkus));
            }
            if (!empty($attrFilters)) {
                $products = Mage::getModel('catalog/product')->getCollection()
                    ->setStoreId($storeId)
                    ->addAttributeToSelect($attr)
                    ->addAttributeToSelect('sku_type')
                    ->addAttributeToFilter($attrFilters);
                foreach ($po->getAllItems() as $item) {
                    $oItem = $item->getOrderItem();
                    if (null === $item->getData('vendor_sku')
                        && ($product = $products->getItemById($item->getProductId()))
                    ) {
                        $item->setVendorSku($product->getData($attr));
                        if ($oItem->getProductType() == 'bundle' && !$product->getSkuType() && $oItem->getChildrenItems()) {
                            $_bundleSkus = array($product->getData($attr) ? $product->getData($attr) : $product->getSku());
                            foreach ($oItem->getChildrenItems() as $oiChild) {
                                if (($childProd = $products->getItemById($oiChild->getProductId()))
                                    && $childProd->getData($attr)
                                ) {
                                    $_bundleSkus[] = $childProd->getData($attr);
                                } else {
                                    $_bundleSkus[] = $oiChild->getSku();
                                }
                            }
                            $item->setVendorSku(implode('-', $_bundleSkus));
                        }
                    } elseif (null === $item->getData('vendor_sku')) {
                        $item->setVendorSku('');
                    }
                    if (null === $item->getData('vendor_simple_sku') && !empty($simpleSkus[spl_object_hash($item)])
                        && $item->getOrderItem()->getProductOptionByCode('simple_sku')
                        && ($product = $products->getItemByColumnValue('sku', $simpleSkus[spl_object_hash($item)]))
                    ) {
                        $item->setVendorSimpleSku((string)$product->getData($attr));
                    } elseif (null === $item->getData('vendor_simple_sku')) {
                        $item->setVendorSimpleSku('');
                    }
                }
            }
        }
        Mage::dispatchEvent('udropship_po_add_vendor_skus', array('po'=>$po, 'attribute_code'=>$attr));
        foreach ($po->getAllItems() as $item) {
            $item->unsFirstAddVendorSkuFlag();
        }
        return $this;
    }

    public function getVendorShipmentsPdf($shipments)
    {
        foreach ($shipments as $shipment) {
            $this->assignVendorSkus($shipment);
            $tracks = $shipment->getOrder()->getTracksCollection();
            $tracks->load();
            foreach ($tracks as $id=>$track) {
                $tracks->removeItemByKey($id);
            }
        }
        $pdf = Mage::getModel('udropship/pdf_shipment')
            ->setUseFont(Mage::getStoreConfig('udropship/vendor/pdf_use_font'))
            ->getPdf($shipments);
        foreach ($shipments as $shipment) {
            $this->unassignVendorSkus($shipment);
        }
        return $pdf;
    }

    protected $_shipmentComments = array();
    public function getVendorShipmentsCommentsCollection($shipment)
    {
        if (!isset($this->_shipmentComments[$shipment->getId()])) {
            $comments = Mage::getResourceModel('sales/order_shipment_comment_collection')
                ->setShipmentFilter($shipment->getId())
                ->addAttributeToFilter('is_visible_to_vendor', 1)
                ->setCreatedAtOrder();

            if (!Mage::helper('udropship')->isSalesFlat()) {
                $comments->addAttributeToSelect('*');
            }

            if ($shipment->getId()) {
                foreach ($comments as $comment) {
                    $comment->setShipment($shipment);
                }
            }
            $this->_shipmentComments[$shipment->getId()] = $comments;
        }
        return $this->_shipmentComments[$shipment->getId()];
    }

    public function applyEstimateTotalPriceMethod($total, $price, $store=null)
    {
        $totalMethod = Mage::getStoreConfig('udropship/customer/estimate_total_method', $store);
        if ($totalMethod=='max') {
            $total = max($total, $price);
        } else {
            $total += $price;
        }
        return $total;
    }

    public function applyEstimateTotalCostMethod($total, $cost, $store=null)
    {
        $total += $cost;
        return $total;
    }

    public function explodeOrderShippingMethod($order)
    {
        $oShippingMethod = explode('_', $order->getShippingMethod(), 2);
        if (!empty($oShippingMethod[1])) {
            $_osm = explode('___', $oShippingMethod[1]);
            $oShippingMethod[1] = $_osm[0];
            if (!empty($_osm[1]) && false !== strpos($_osm[1], '_')) {
                $__osm = explode('___', $_osm[1]);
                $oShippingMethod[2] = $__osm[0];
            }
        }
        return $oShippingMethod;
    }

    public function initVendorShippingMethodsForHtmlSelect($order, &$vMethods)
    {
        $oShippingMethod = Mage::helper('udropship')->explodeOrderShippingMethod($order);
        $carrierNames = Mage::getSingleton('udropship/source')->getCarriers();
        $shipping = $this->getShippingMethods();
        if ('order' == Mage::getStoreConfig('udropship/vendor/reassign_available_shipping')
            && $oShippingMethod[0] == 'udropship' && !empty($oShippingMethod[1])
        ) {
            $oShipping = $shipping->getItemByColumnValue('shipping_code', $oShippingMethod[1]);
        }
        $oShippingDetails = Zend_Json::decode($order->getUdropshipShippingDetails());
        foreach ($vMethods as $vId => &$vMethod) {
            if ($vMethod === false) continue;
            $v = $this->getVendor($vId);
            $vSMs = $v->getShippingMethods();
            foreach ($vSMs as $sId => $vSM) {
                if (isset($oShipping) && $sId != $oShipping->getId()) continue;
                $s = $shipping->getItemById($sId);
                list($sc, $cc) = array($s->getShippingCode(), $vSM['carrier_code']);
                $ccs = array($cc);
                if ($cc!=$v->getCarrierCode()) $ccs[] = $v->getCarrierCode();
                foreach ($ccs as $i=>$cc) {
                    $mc = $s->getSystemMethods($cc);
                    if (empty($sc) || empty($cc) || empty($mc)) continue;
                    $cMethodNames = $this->getCarrierMethods($cc);
                    if ($mc == '*') {
                        $_mc = is_array($cMethodNames) ? array_keys($cMethodNames) : array();
                    } else {
                        $_mc = array($mc);
                    }
                    foreach ($_mc as $mc) {
                        $vMethod[$sc]['__title'] = $s->getShippingTitle();
                        $ccMcKeys = array(sprintf('%s_%s', $cc, $mc));
                        if ($this->hasExtraChargeMethod($v, $vSM)) {
                            $ccMcKeys[] = sprintf('%s_%s___ext', $cc, $mc);
                        }
                        foreach ($ccMcKeys as $ccMcKey) {
                            if ($oShippingMethod[0] == 'udropship' && !empty($oShippingMethod[1])
                                && $sc==$oShippingMethod[1] && $i==0
                            ) {
                                if (empty($oShippingMethod[2]) || $oShippingMethod[2] == $ccMcKey) {
                                    $vMethod[$sc][$ccMcKey]['__selected'] = true;
                                }
                            } elseif ($oShippingMethod[0] == 'udsplit'
                                && is_array($oShippingDetails)
                                && !empty($oShippingDetails['methods'][$vId]['code'])
                                && $oShippingDetails['methods'][$vId]['code'] == $ccMcKey
                            ) {
                                $vMethod[$sc][$ccMcKey]['__selected'] = true;
                            }
                            if (false !== strpos($ccMcKey, '___ext')) {
                                $vMethod[$sc][$ccMcKey][$ccMcKey] = sprintf('%s - %s %s', $carrierNames[$cc], $cMethodNames[$mc], $this->getExtraChargeData($v, $vSM, 'extra_charge_suffix'));
                            } else {
                                $vMethod[$sc][$ccMcKey][$ccMcKey] = sprintf('%s - %s', $carrierNames[$cc], $cMethodNames[$mc]);
                            }
                        }
                    }
                }
            }
        }
        unset($vMethod);
    }

    public function createOnDuplicateExpr($conn, $fields)
    {
        $updateFields = array();
        foreach ($fields as $k => $v) {
            $field = $value = null;
            if (!is_numeric($k)) {
                $field = $conn->quoteIdentifier($k);
                if ($v instanceof Zend_Db_Expr) {
                    $value = $v->__toString();
                } else if (is_string($v)) {
                    $value = 'VALUES('.$conn->quoteIdentifier($v).')';
                } else if (is_numeric($v)) {
                    $value = $conn->quoteInto('?', $v);
                }
            } else if (is_string($v)) {
                $field = $conn->quoteIdentifier($v);
                $value = 'VALUES('.$field.')';
            }

            if ($field && $value) {
                $updateFields[] = "{$field}={$value}";
            }
        }
        return $updateFields ? (" ON DUPLICATE KEY UPDATE " . join(', ', $updateFields)) : '';
    }

    public function getAdjustmentPrefix($type)
    {
        switch ($type) {
            case 'po_comment':
                return 'po-comment-';
            case 'shipment_comment':
                return 'shipment-comment-';
            case 'statement':
                return 'statement-';
            case 'payout':
                return 'payout-';
            case 'statement:payout':
                return 'statement:payout-';
        }
        return '';
    }

    public function isAdjustmentComment($comment, $store=null)
    {
        $adjTrigger = Mage::getStoreConfig('udropship/statement/adjustment_trigger', $store).':';
        $adjTriggerQ = preg_quote($adjTrigger);
        return preg_match("#({$adjTriggerQ})\\s*([0-9.-]+)\\s*(.*)\$#m", $comment);
    }
    public function collectPoAdjustments($pos, $force=false)
    {
        $adjTrigger = Mage::getStoreConfig('udropship/statement/adjustment_trigger').':';
        $adjTriggerQ = preg_quote($adjTrigger);
        $posToCollect = array();
        foreach ($pos as $po) {
            if (!$po->hasAdjustments() || $force) {
                $posToCollect[$po->getId()] = $po;
            }
        }
        if (!empty($posToCollect)) {
            $poType = $pos instanceof Varien_Data_Collection && $pos->getFirstItem() instanceof Unirgy_DropshipPo_Model_Po
                || reset($pos) instanceof Unirgy_DropshipPo_Model_Po
                ? 'po' : 'shipment';
            $comments = $adjustments = $adjAmounts = array();
            if ($poType == 'po') {
                $commentsCollection = Mage::getModel('udpo/po_comment')->getCollection()
                    ->addAttributeToFilter('parent_id', array('in'=>array_keys($posToCollect)))
                    ->addAttributeToFilter('comment', array('like'=>$adjTrigger.'%'))
                    ->addAttributeToSelect('*')
                    ->addAttributeToSort('created_at');
                $commentsCollection->getSelect()->columns(array('po_id'=>'parent_id', 'adjustment_prefix_type'=>new Zend_Db_Expr("'po_comment'")));
                $comments[] = $commentsCollection;
            }
            $commentsCollection = Mage::getModel('sales/order_shipment_comment')->getCollection()
                ->addAttributeToFilter('comment', array('like'=>$adjTrigger.'%'))
                ->addAttributeToSelect('*')
                ->addAttributeToSort('created_at');
            if ($poType == 'po') {
                $commentsCollection->getSelect()->join(
                    array('sos' => $commentsCollection->getTable('sales/shipment')),
                    'sos.entity_id=main_table.parent_id',
                    array()
                );
                $commentsCollection->getSelect()->where('sos.udpo_id in (?)', array_keys($posToCollect));
                $commentsCollection->getSelect()->columns(array('po_id'=>'sos.udpo_id'));
            } else {
                $commentsCollection->addAttributeToFilter('parent_id', array('in'=>array_keys($posToCollect)));
                $commentsCollection->getSelect()->columns(array('po_id'=>'parent_id'));
            }
            $commentsCollection->getSelect()->columns(array('adjustment_prefix_type'=>new Zend_Db_Expr("'shipment_comment'")));
            $comments[] = $commentsCollection;
            foreach ($comments as $_comments) {
                foreach ($_comments as $comment) {
                    if (!preg_match("#({$adjTriggerQ})\\s*([0-9.-]+)\\s*(.*)\$#m", $comment->getComment(), $match)) {
                        continue;
                    }
                    $sId = $comment->getPoId();
                    if (!isset($adjAmounts[$sId])) {
                        $adjAmounts[$sId] = 0;
                        $adjustments[$sId] = array();
                    }
                    $adjKey = $this->getAdjustmentPrefix($comment->getAdjustmentPrefixType()).$comment->getId();
                    $adjustments[$sId][$adjKey] = array(
                        'adjustment_id' => $adjKey,
                        'po_id' => $posToCollect[$sId]->getIncrementId(),
                        'po_type' => $poType,
                        'amount' => (float)$match[2],
                        'comment' => $match[1].' '.$match[3],
                        'created_at' => $comment->getCreatedAt(),
                    	'username' => $comment->getUsername(),
                    );
                    $adjAmounts[$sId] += (float)$match[2];
                }
            }
            foreach ($posToCollect as $sId => $po) {
                if (isset($adjAmounts[$sId])) {
                    $po->setAdjustmentAmount($adjAmounts[$sId]);
                    $po->setAdjustments($adjustments[$sId]);
                } else {
                    $po->setAdjustmentAmount(0);
                    $po->setAdjustments(array());
                }
            }
        }
        return $this;
    }

    protected $_emptyStatementTotalsAmount = array(
        'subtotal'=>0, 'tax'=>0, 'shipping'=>0, 'handling'=>0,
        'com_amount'=>0, 'trans_fee'=>0, 'adj_amount'=>0, 'total_payout'=>0
    );
    protected $_emptyStatementCalcTotalsAmount = array(
        'total_paid' => 0
    );
    protected $_emptyStatementCalcTotals;
    protected $_emptyStatementTotals;

    protected function _getStatementEmptyTotalsAmount($calc=false, $format=false)
    {
        if (!$calc) {
            $est  = &$this->_emptyStatementTotals;
            $esta = &$this->_emptyStatementTotalsAmount;
        } else {
            $est  = &$this->_emptyStatementCalcTotals;
            $esta = &$this->_emptyStatementCalcTotalsAmount;
        }
        if ($format && is_null($est)) {
            $this->formatAmounts($est, $esta, true);
        }
        return $format ? $est : $esta;
    }

    public function getStatementEmptyTotalsAmount($format=false)
    {
        return $this->_getStatementEmptyTotalsAmount(false, $format);
    }

    public function getStatementEmptyCalcTotalsAmount($format=false)
    {
        return $this->_getStatementEmptyTotalsAmount(true, $format);
    }

    public function formatAmounts(&$data, $defaultAmounts=null, $useDefault=false)
    {
        $core = Mage::helper('core');
        $iter = (is_null($defaultAmounts) ? $data : $defaultAmounts);
        if (is_array($iter)) {
            foreach ($iter as $k => $v) {
                if ($useDefault == 'merge' || $useDefault && !isset($data[$k])) {
                    $data[$k] = $core->formatPrice($v, false);
                } elseif (isset($data[$k])) {
                    $data[$k] = $core->formatPrice($data[$k], false);
                }
            }
        }
        return $this;
    }

    public function getStatementEmptyOrderAmounts($format=false)
    {
        return $this->getStatementEmptyTotalsAmount($format);
    }

    public function getPoOrderIncrementId($po)
    {
        return $po->hasOrderIncrementId() ? $po->getOrderIncrementId() : $po->getOrder()->getIncrementId();
    }

    public function getPoOrderCreatedAt($po)
    {
        return $po->hasOrderCreatedAt() ? $po->getOrderCreatedAt() : $po->getOrder()->getCreatedAt();
    }

    public function getItemStockCheckQty($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            if ($item->hasUdpoCreateQty()) {
                return $item->getUdpoCreateQty();
            } {
                return $this->isUdpoActive()
                    ? Mage::helper('udpo')->getOrderItemQtyToUdpo($item, true)
                    : $item->getQtyOrdered()-$item->getQtyCanceled()-$item->getQtyRefunded();
            }
        } else {
            $parentQty = $item->getParentItem() ? $item->getParentItem()->getQty() : 1;
            return $item->getQty()*$parentQty;
        }
    }

	public function getZipcodeByItem($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            return $item->getOrder()->getShippingAddress()
            	? $item->getOrder()->getShippingAddress()->getPostcode()
            	: ($item->getOrder()->getBillingAddress()
            		? $item->getOrder()->getBillingAddress()->getPostcode()
            		: null
            	);
        } else {
	        $address = $item->getQuote() ? $item->getQuote()->getShippingAddress() : null;
            if ($item->getAddress()) {
                $address = $item->getAddress();
            }
            return $address ? $address->getPostcode() : null;
        }
    }

    public function getItemBaseCost($item, $altCost=null)
    {
        $result = abs($altCost)<0.001 ? (abs($item->getBaseCost())<0.001 ? $item->getBasePrice() : $item->getBaseCost()) : $altCost;
        return abs($altCost)<0.001 ? (abs($item->getBaseCost())<0.001 ? $item->getBasePrice() : $item->getBaseCost()) : $altCost;
    }

    public function getSalesEntityVendors($entity)
    {
        if (!is_callable(array($entity, 'getAllItems'))) return array();
        $products = array();
        foreach ($entity->getAllItems() as $si) {
            $products[$si->getProductId()][] = $si;
        }
        $read = Mage::getSingleton('core/resource')->getConnection('udropship_read');
        $attr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'udropship_vendor');
        $table = $attr->getBackend()->getTable();
        $select = $read->select()
            ->from($table, array('entity_id', 'value'))
            ->where('attribute_id=?', $attr->getId())
            ->where('entity_id in (?)', array_keys($products));
        $rows = $read->fetchPairs($select);
        $result = array();
        foreach ($products as $pId => $siArr) {
            foreach ($siArr as $item) {
                if (Mage::getStoreConfig('udropship/stock/availability', $entity->getStoreId())=='local_if_in_stock') {
                    $result[$item->getId()][$this->getLocalVendorId($entity->getStoreId())] = true;
                }
                $children = $item->getChildrenItems() ? $item->getChildrenItems() : $item->getChildren();
                if (!empty($children)) {
                    foreach ($children as $child) {
                        if (Mage::getStoreConfig('udropship/stock/availability', $entity->getStoreId())=='local_if_in_stock') {
                            $result[$child->getId()][$this->getLocalVendorId($entity->getStoreId())] = true;
                        }
                        if (!empty($rows[$child->getProductId()])) $result[$item->getId()][$rows[$child->getProductId()]] = true;
                    }
                } else {
                    if (!empty($rows[$item->getProductId()])) $result[$item->getId()][$rows[$item->getProductId()]] = true;
                }
            }
        }
        return $result;
    }

    public function getVendorShipmentStatuses()
    {
        if (Mage::getStoreConfig('udropship/vendor/is_restrict_shipment_status')) {
            $shipmentStatuses = Mage::getStoreConfig('udropship/vendor/restrict_shipment_status');
            if (!is_array($shipmentStatuses)) {
                $shipmentStatuses = explode(',', $shipmentStatuses);
            }
            return Mage::getSingleton('udropship/source')->setPath('shipment_statuses')->getOptionLabel($shipmentStatuses);
        } else {
            return Mage::getSingleton('udropship/source')->setPath('shipment_statuses')->toOptionHash();
        }
    }

    public function getVendorTracksCollection($shipment)
    {
        return $shipment->getTracksCollection()->setOrder('master_tracking_id');
    }

    public function isUdropshipOrder($order)
    {
    	if (!$order instanceof Mage_Sales_Model_Order) return false;
    	$oSM = Mage::helper('udropship')->explodeOrderShippingMethod($order);
		return in_array($oSM[0], array('udropship', 'udsplit'));
    }

    public function getOrderItemById($order, $itemId)
    {
    	$orderItem = $order->getItemById($itemId);
    	if (!$orderItem) {
	    	foreach ($this->getItemsCollection() as $item) {
	            if ($item->getId()==$itemId) {
	                return $item;
	            }
	        }
    	}
    	return $orderItem;
    }

    public function addShipmentComment($shipment, $comment, $visibleToVendor=true, $isVendorNotified=false, $isCustomerNotified=false)
    {
        if (!$comment instanceof Mage_Sales_Model_Order_Shipment_Comment) {
            $statuses = Mage::getSingleton('udropship/source')->setPath('shipment_statuses')->toOptionHash();
            $comment = Mage::getModel('sales/order_shipment_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($isCustomerNotified)
                ->setIsVendorNotified($isVendorNotified)
                ->setIsVisibleToVendor($visibleToVendor)
                ->setUdropshipStatus(@$statuses[$shipment->getUdropshipStatus()]);
        }
        $shipment->addComment($comment);
        return $this;
    }

    public function processShipmentStatusSave($shipment, $status)
    {
        if ($shipment->getUdropshipStatus() != $status) {
            $oldStatus = $shipment->getUdropshipStatus();
            Mage::dispatchEvent(
                'udropship_shipment_status_save_before',
                array('shipment'=>$shipment, 'old_status'=>$oldStatus, 'new_status'=>$status)
            );
            $comment = sprintf("[Shipment status changed from '%s' to '%s']",
                $this->getShipmentStatusName($shipment->getUdropshipStatus()),
                $this->getShipmentStatusName($status)
            );
            $shipment->setUdropshipStatus($status);
            $shipment->getResource()->saveAttribute($shipment, 'udropship_status');
            $this->addShipmentComment($shipment, $comment);
            $shipment->getCommentsCollection()->save();
            Mage::dispatchEvent(
                'udropship_shipment_status_save_after',
                array('shipment'=>$shipment, 'old_status'=>$oldStatus, 'new_status'=>$status)
            );
        }
        return $this;
    }

    public function processPoStatusSave($po, $status)
    {
        if ($po instanceof Unirgy_DropshipPo_Model_Po) {
            Mage::helper('udpo')->processPoStatusSave($po, $status, true);
        } elseif ($po instanceof Unirgy_DropshipStockPo_Model_Po) {
            Mage::helper('ustockpo')->processPoStatusSave($po, $status, true);
        } else {
            Mage::helper('udropship')->processShipmentStatusSave($po, $status);
        }
        return $this;
    }

    function array_merge_2(&$array, &$array_i) {
        // For each element of the array (key => value):
        foreach ($array_i as $k => $v) {
            // If the value itself is an array, the process repeats recursively:
            if (is_array($v)) {
                if (!isset($array[$k])) {
                    $array[$k] = array();
                }
                $this->array_merge_2($array[$k], $v);

            // Else, the value is assigned to the current element of the resulting array:
            } else {
                if (isset($array[$k]) && is_array($array[$k])) {
                    $array[$k][0] = $v;
                } else {
                    if (isset($array) && !is_array($array)) {
                        $temp = $array;
                        $array = array();
                        $array[0] = $temp;
                    }
                    $array[$k] = $v;
                }
            }
        }
    }


    function array_merge_n() {
        // Initialization of the resulting array:
        $array = array();

        // Arrays to be merged (function's arguments):
        @$arrays =& func_get_args();

        // Merging of each array with the resulting one:
        foreach ($arrays as $array_i) {
            if (is_array($array_i)) {
                $this->array_merge_2($array, $array_i);
            }
        }

        return $array;
    }

    public function isUrlKeyReserved($urlKey)
    {
        $front = Mage::app()->getFrontController();
        $admin = $front->getRouter('admin');
        $standard = $front->getRouter('standard');
        return $admin && ($admin->getFrontNameByRoute($urlKey) || $admin->getRouteByFrontName($urlKey))
            || $standard && ($standard->getFrontNameByRoute($urlKey) || $standard->getRouteByFrontName($urlKey))
        ;
    }

    public function hasExtraChargeMethod($vendor, $vMethod)
    {
        return $vendor->getAllowShippingExtraCharge() && @$vMethod['allow_extra_charge'];
    }
    public function getExtraChargeData($vendor, $vMethod, $field)
    {
        return null !== @$vMethod[$field] ? $vMethod[$field] : $vendor->getData('default_shipping_'.$field);
    }
    public function getExtraChargeRate($request, $rate, $vendor, $vMethod)
    {
        $vendor = $this->getVendor($vendor);
        if ($this->hasExtraChargeMethod($vendor, $vMethod)) {
            $exRate = clone $rate;
            $fields = array();
            foreach (array(
                'extra_charge_suffix','extra_charge_type','extra_charge'
            ) as $field) {
                $fields[$field] = $this->getExtraChargeData($vendor, $vMethod, $field);
            }
            $exRate->setSuffix(' '.$fields['extra_charge_suffix']);
            $exRate->setMethod($exRate->getMethod().'___ext');
            $exRate->setMethodTitle($exRate->getMethodTitle().' '.$fields['extra_charge_suffix']);
            switch ($fields['extra_charge_type']) {
                case 'shipping_percent':
                    $exPrice = $exRate->getPrice()*abs($fields['extra_charge'])/100;
                    break;
                case 'subtotal_percent':
                    $exPrice = $request->getPackageValue()*abs($fields['extra_charge'])/100;
                    break;
                case 'fixed':
                    $exPrice = abs($fields['extra_charge']);
                    break;
            }
            $exRate->setBeforeExtPrice($exRate->getPrice());
            $exRate->setPrice($exRate->getPrice()+$exPrice);
            $exRate->setIsExtraCharge(true);
            $rate->setHasExtraCharge(true);
            $exRate->setHasExtraCharge(true);
            return $exRate;
        }
        return false;
    }

    public function dateLocaleToInternal($date, $format=null, $recalc=false)
    {
        static $dateObj;
        if (null === $dateObj) {
            $dateObj = Mage::app()->getLocale()->date();
        }
        $format = is_null($format) ? Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : $format;
        $dateObj->set($date, $format);
        if ($recalc) {
            $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        }
        $result = $dateObj->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        if ($recalc) {
            if ($timezone = Mage::app()->getStore()->getConfig(self::XML_PATH_DEFAULT_TIMEZONE)) {
                $dateObj->setTimezone($timezone);
            }
        }
        return $result;
    }
    public function dateInternalToLocale($date, $format=null, $recalc=false)
    {
        static $dateObj;
        if (null === $dateObj) {
            $dateObj = Mage::app()->getLocale()->date();
            $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        }
        $format = is_null($format) ? Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : $format;
        $dateObj->set($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
        if ($recalc) {
            if ($timezone = Mage::app()->getStore()->getConfig(self::XML_PATH_DEFAULT_TIMEZONE)) {
                $date->setTimezone($timezone);
            }
        }
        $result = $dateObj->toString($format);
        if ($recalc) {
            $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        }
        return $result;
    }
    public function mapSystemToUdropshipMethod($code, $vendor)
    {
        $vendor = Mage::helper('udropship')->getVendor($vendor);
        $systemMethods = Mage::helper('udropship')->getShippingMethods();
        $vendorMethods = $vendor->getShippingMethods();
        $found = false;
        foreach ($vendorMethods as $vendorMethod) {
            if ($code == $vendorMethod['carrier_code'].'_'.$vendorMethod['method_code']) {
                $found = $vendorMethod['shipping_id'];
                break;
            }
        }
        if (!$found) {
            foreach ($systemMethods as $systemMethod) {
                foreach ($systemMethod->getSystemMethods() as $sc=>$sm) {
                    if ($code == $sc.'_'.$sm
                        || $sm == '*' && 0 === strpos($code, $sc.'_')
                    ) {
                        $found = $systemMethod->getId();
                        break;
                    }
                }
            }
        }
        static $unknown;
        if (null === $unknown) {
            $unknown = Mage::getModel('udropship/shipping')->setData(array(
                'shipping_code' => '***unknown***',
                'shipping_title' => '***Unknown***',
            ));
        }
        return $found && $systemMethods->getItemById($found) ? $systemMethods->getItemById($found) : $unknown;
    }

    public function formatCustomerAddress($address, $format, $vendor)
    {
        $vendor = Mage::helper('udropship')->getVendor($vendor);
        $address->setData('__udropship_vendor', $vendor);
        $result = $address->format($format);
        $address->unsetData('__udropship_vendor');
        return $result;
    }

    public function filterObjectsInDump($data)
    {
        $result = '';
        if (is_array($data) || $data instanceof Varien_Data_Collection) {
            $result = array();
            foreach ($data as $k=>$v) {
                if ($v instanceof Varien_Object) {
                    $_v = $this->filterObjectsInDump($v->getData());
                    array_unshift($_v, spl_object_hash($v));
                    array_unshift($_v, get_class($v));
                } elseif ($v instanceof Varien_Data_Collection) {
                    $_v = $this->filterObjectsInDump($v);
                } elseif (is_array($v)) {
                    $_v = $this->filterObjectsInDump($v);
                } elseif (is_object($v)) {
                    $_v = get_class($v)." - ".spl_object_hash($v)."\n\n".$v;
                } else {
                    $_v = $v;
                }
                $result[$k] = $_v;
            }
            if ($data instanceof Varien_Data_Collection_Db) {
                array_unshift($result, $data->getSelect().'');
            }
            if ($data instanceof Varien_Data_Collection) {
                array_unshift($result, spl_object_hash($data));
                array_unshift($result, get_class($data));
            }
        } elseif ($data instanceof Varien_Object) {
            $result = $this->filterObjectsInDump($data->getData());
            array_unshift($result, spl_object_hash($data));
            array_unshift($result, get_class($data));
        } elseif (is_object($data)) {
            $result = get_class($data)." - ".spl_object_hash($data)."\n\n".$data;
        } else {
            $result = $data;
        }
        return $result;
    }

    static protected $_dtlIps = array('109.254.31.221');

    public function dump($data, $file = '')
    {
        //if (!in_array($_SERVER['REMOTE_ADDR'], self::$_dtlIps)) return ;
        ob_start();
        $filtered = $this->filterObjectsInDump($data);
        is_array($filtered) ? print_r($filtered) : var_dump($filtered);
        //Mage::log(ob_get_clean(), null, $file);
        file_put_contents(BP.DS.'var'.DS.'log'.DS.$file, ob_get_clean(), FILE_APPEND);
    }




		public function removeDates($vendorId)
		{
			$dates = Mage::getModel('udropship/vendor_nondate')->getCollection()->addFieldToSelect('*')->addFieldToFilter('vendor_id', $vendorId);
			if(count($dates) > 0)
			{
				foreach($dates as $val)
				{
					$val->delete();
				}
			}
		}

		public function getDates($vendorId)
		{
			$output = array();
			$dates = Mage::getModel('udropship/vendor_nondate')->getCollection()->addFieldToSelect('*')->addFieldToFilter('vendor_id', $vendorId);
			if(count($dates) > 0)
			{
				foreach($dates as $val)
				{
					$output[] = $val->getDate();
				}
			}
			return $output;
		}

		public function addDates($vendorId, $dates)
		{
			foreach($dates as $val)
			{
				if(!empty($val))
				{
					$model = Mage::getModel('udropship/vendor_nondate');
					$model->setVendorId($vendorId);
					$model->setDate(date('Y-m-d', strtotime($val)));
					$model->save();
				}
			}
		}

		public function removeDays($vendorId)
		{
			$dates = Mage::getModel('udropship/vendor_nonday')->getCollection()->addFieldToSelect('*')->addFieldToFilter('vendor_id', $vendorId);
			if(count($dates) > 0)
			{
				foreach($dates as $val)
				{
					$val->delete();
				}
			}
		}

		public function getDays($vendorId)
		{
			$output = array();
			$dates = Mage::getModel('udropship/vendor_nonday')->getCollection()->addFieldToSelect('*')->addFieldToFilter('vendor_id', $vendorId);
			if(count($dates) > 0)
			{
				foreach($dates as $val)
				{
					$output[] = $val->getDay();
				}
			}
			return $output;
		}

		public function addDays($vendorId, $dates)
		{
			foreach($dates as $val)
			{
				if(!empty($val))
				{
					$model = Mage::getModel('udropship/vendor_nonday');
					$model->setVendorId($vendorId);
					$model->setDay($val);
					$model->save();
				}
			}
		}

    public function getDeliveryFromQuoteVendors($quote = null) {
        if(!$quote){
            $quote = Mage::getSingleton('checkout/cart')->getQuote();
        }
        $priceChangeAmount = 0;
        foreach($quote->getAllItems() as $item){
            $vendorsArr[] = $item->getUdropshipVendor();
        }
        $vendorsArr = array_unique($vendorsArr);
        //coupon free shipping
        $couponCode = $quote->getCouponCode();
        $fvid = false;
        if($couponCode)
        {
            $rule = Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('code',array('eq'=>$couponCode))->getFirstItem();
            if($rule['simple_free_shipping']){
                $vd = Mage::getModel('udropship/vendor_coupon')->getCollection()->addFieldToFilter('coupon_id',array('eq'=>$rule->getId()))->getFirstItem();
                $fvid = $vd->getVendorId();
            }
        }
        foreach($vendorsArr as $vendor) {

            $_v = $this->getVendor($vendor);
            if($fvid == $vendor){
                $priceChangeAmount = $priceChangeAmount;;
            }else{
                $priceChangeAmount += $_v->getDeliveryCharge();
            }
        }
        $shipmentCost = (float)$priceChangeAmount;
        
        return $shipmentCost;
    }
    
    /* Get category by vendor id
     *
     * @param $vendorId -- vendor id
     *
     * @return Mage_Catalog_Model_Category_Collection item $cat or false
     */
    public function getCategoryByVendor($vendorId) {
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('yelp_api',$vendorId)
            ->setCurPage(1)->setPageSize(1);
        $_cat = $collection->getFirstItem();
        $data = $_cat->getData();
        return (empty($data)) ? false : $_cat;
    }
    
    /* Get vendor by subdomain
     *
     * @param string $subdomain -- subdomain bane
     *
     * @return int $vendorId or false
     */
    public function getVendorBySubdomain($subdomain) {
        $query = "SELECT vendor_id FROM udropship_vendor WHERE  subdomain = '$subdomain'";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        return $result;
    }
    
    /* Get vendor by subdomain
     *
     * @param string $subdomain -- subdomain bane
     *
     * @return int $vendorId or false
     */
    public function getVendorByUsername($username) {
        $query = "SELECT vendor_id FROM udropship_vendor WHERE  email = '$username'";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        return $result;
    }
}
