<?php
class SMDesign_Colorswatchproductview_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Media {
	
    public function getGalleryUrl($image=null) {
				$pid = Mage::getModel('catalog/session')->getCurrentSimpleProductId();
				$params = array('id'=> ($pid ? $pid : $this->getProduct()->getId()));

        if ($image) {
            $params['image'] = $image->getValueId();
            return $this->getUrl('*/*/gallery', $params);
        }
        return $this->getUrl('*/*/gallery', $params);
    }
    
    function changeTemplate($template) {
    	if ($this->getProduct()->getData('enable_zoom_plugin') == 1 && !defined('SMD_LICENSE_ERROR')) {
    		$this->setTemplate($template);
    	}
    }
    
}