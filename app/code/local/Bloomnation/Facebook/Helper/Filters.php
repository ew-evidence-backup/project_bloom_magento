<?php

class Bloomnation_Facebook_Helper_Filters extends Mage_Core_Helper_Abstract {
    /* Get HTML for a select box input
     *
     * @param string $attrCode also it as an attribute code for Magento
     *
     * return string $html
     */
    public function getFilterHtml($attrCode, $value, $type='', $template='facebook/filter/filter.phtml',$image=false) {
        $block = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Template',
            'html_filter_block_'.$attrCode,
            array('template' => $template));
        $attrObj = Mage::getModel('eav/config')->getAttribute('catalog_product',$attrCode);
        $block->setAttrCode($attrCode);
        $block->setAttrObj($attrObj);
        if($image)
        {
            $block->setAttrImages($this->getOptionImages($attrObj->getId()));
        }
        return $block->toHtml();
    }
    public function getOptionImages($attrId)
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_write');
        $qry = "SELECT * FROM gomage_navigation_attribute_option where attribute_id = ".(int)$attrId;
        $result = $db->query($qry);
        if(!$result) {
            return FALSE;
        }
        $rows = $result->fetchAll();
        if(!$rows) {
            return FALSE;
        }
        $rs = array();
        foreach($rows as $row)
        {
            $rs[$row['option_id']] = $row['filename'];
        }
        return $rs;
    }
}