<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Adminhtml block for fieldset of bundle product
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Bundle
    extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    /**
     * Returns string with json config for bundle product
     *
     * @return string
     */
    public function getJsonConfig() {
        $options = array();
        $optionsArray = $this->getOptions();
        foreach ($optionsArray as $option) {
            $optionId = $option->getId();
            $options[$optionId] = array('id' => $optionId, 'selections' => array());
            foreach ($option->getSelections() as $selection) {
                $options[$optionId]['selections'][$selection->getSelectionId()] = array(
                    'can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'default_qty'    => $selection->getSelectionQty()
                );
            }
        }
        $config = array('options' => $options);
        return Mage::helper('core')->jsonEncode($config);
    }
}
