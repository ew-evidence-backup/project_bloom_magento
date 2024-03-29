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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Catalog Product Price Template Block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Price_Template extends Mage_Core_Block_Abstract
{
    /**
     * Product Price block types cache
     *
     * @var array
     */
    protected $_priceBlockTypes = array();

    /**
     * Retrieve array of Price Block Types
     *
     * Key is price block type name and value is array of
     * template and block strings
     *
     * @return array
     */
    public function getPriceBlockTypes()
    {
        return $this->_priceBlockTypes;
    }

    /**
     * Adding customized price template for product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Mage_Catalog_Block_Product_Price_Template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array(
                'block'     => $block,
                'template'  => $template
            );
        }

        return $this;
    }
}
