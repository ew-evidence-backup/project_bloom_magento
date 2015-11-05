<?php

class Impl_Custom_Model_Observer {

    public function check_zip_codes() {
        $_SESSION['zip_refer'] = $this->getCurrentUrl();
	
        if ((Mage::app()->getRequest()->getModuleName() == 'catalog' && Mage::app()->getRequest()->getControllerName() == 'product') || Mage::app()->getRequest()->getModuleName() == 'review') {
            if (isset($_COOKIE["bloom_nations_zipcode"])) {
                $product = Mage::registry('product', null);
				$category = Mage::registry('current_category', null);
				//is this a florist category?
				if(!$category == null)
				{
					$parent_ids = $category->getPathIds();
					$noCat = false;
				}
				else
				{
					$noCat = true;
				}
				if(in_array(20,$parent_ids))
				{
					$floristParent = true;
				}
                if ($product != null) {
					if($floristParent == true && $noCat == true)
					{
						$collection = Mage::getModel('custom/productzip')->getCollection()
                            ->addFieldToFilter('product_id', $product->getId());
					}
					else
					{
                    $collection = Mage::getModel('custom/productzip')->getCollection()
                            ->addFieldToFilter('product_id', $product->getId());
					}

                    if (count($collection) < 1) {
                        $collection = Mage::getModel('custom/productzip')->getCollection()
                                ->addFieldToFilter('product_id', $product->getId());

                        if (count($collection) < 1) {
                            //Mage::app()->getFrontController()->getResponse()->setRedirect('/flowers.html');
                        }
                    }
                }
                //}
            }
        } else if ((Mage::app()->getRequest()->getModuleName() == 'catalog' && Mage::app()->getRequest()->getControllerName() == 'category')) {
            $category = Mage::registry('category', null);
            if ($category != null) {

            }
        }
    }

    public function setOrderComment(Varien_Event_Observer $observer) {
        $_order = $observer->getEvent()->getOrder();
        $_request = Mage::app()->getRequest();

        $_comments = strip_tags($_request->getParam('implComments'));
        if (!empty($_comments)) {
            $_order->setCustomerNote($_comments);
        }
        return $this;
    }

    protected function getCurrentUrl() {
        $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

}