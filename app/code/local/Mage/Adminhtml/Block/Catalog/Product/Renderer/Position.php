<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Position extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        return  $value;
    }
}