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
 * Product options block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Options extends Mage_Core_Block_Template
{
    protected $_product;

    protected $_optionRenders = array();

    public function __construct()
    {
        parent::__construct();
        $this->addOptionRenderer(
            'default',
            'catalog/product_view_options_type_default',
            'catalog/product/view/options/type/default.phtml'
        );
    }

    /**
     * Retrieve product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('catalog/product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Block_Product_View_Options
     */
    public function setProduct(Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Add option renderer to renderers array
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Mage_Catalog_Block_Product_View_Options
     */
    public function addOptionRenderer($type, $block, $template)
    {
        $this->_optionRenders[$type] = array(
            'block' => $block,
            'template' => $template,
            'renderer' => null
        );
        return $this;
    }

    /**
     * Get option render by given type
     *
     * @param string $type
     * @return array
     */
    public function getOptionRender($type)
    {
        if (isset($this->_optionRenders[$type])) {
            return $this->_optionRenders[$type];
        }

        return $this->_optionRenders['default'];
    }

    public function getGroupOfOption($type)
    {
        $group = Mage::getSingleton('catalog/product_option')->getGroupByType($type);

        return $group == '' ? 'default' : $group;
    }

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getProduct()->getOptions();
    }

    public function hasOptions()
    {
        if ($this->getOptions()) {
            return true;
        }
        return false;
    }

    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getOptions() as $option) {
            /* @var $option Mage_Catalog_Model_Product_Option */
            $priceValue = 0;
            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($option->getValues() as $value) {
                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
                   $_tmpPriceValues[$value->getId()] = Mage::helper('core')->currency($value->getPrice(true), false, false);
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = Mage::helper('core')->currency($option->getPrice(true), false, false);
            }
            $config[$option->getId()] = $priceValue;
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template']);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}
