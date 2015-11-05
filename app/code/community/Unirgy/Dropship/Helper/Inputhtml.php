<?php

class Unirgy_Dropship_Helper_Inputhtml extends Mage_Core_Helper_Abstract {
    /* Get HTML for a select box input
     *
     * @param string $inputName also it as an attribute code for Magento
     * @param string $currentValue select's current value
     * @param string $type if its select or multiselect
     *
     * return string $html
     */
    public function getSelectHtml($inputName, $value, $type='select') {
        $block = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Template',
            'html_select_block_'.$inputName,
            array('template' => 'unirgy/dropship/manage/inputhtml/'.$type.'.phtml'));
        $block->setCurValue($value);
        $block->setAttrName($inputName);
        return $block->toHtml();
    }
}
?>