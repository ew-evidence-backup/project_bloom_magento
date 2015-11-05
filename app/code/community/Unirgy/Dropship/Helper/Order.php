<?php

class Unirgy_Dropship_Helper_Order extends Mage_Core_Helper_Abstract
{
    
    public function sales_order_save_after($order) {
        $products = Mage::getResourceModel('sales/order_item_collection')
                        ->setOrderFilter($order->getId());
        foreach($products as $prod) {
            $data = $prod->getProductOptions();
            $newDate = date('Y-m-d',strtotime($data['info_buyRequest']['delivery_date']));
            
            $query = "UPDATE sales_flat_order_item
                                SET delivery_date = '$newDate'
                                WHERE item_id=".$prod->getId();
            try {
                Mage::getSingleton('core/resource')->getConnection('core_write')->query($query);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }        
    }
    
}