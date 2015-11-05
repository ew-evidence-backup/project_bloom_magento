<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Pimage extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if (empty($row['image'])) return '';
        return '<img src="'. Mage::helper('catalog/image')->init($row, 'image', $row['image'])->resize(150)->__toString
        (). '" />';
    }
}