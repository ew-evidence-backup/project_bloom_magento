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
 * @package     Mage_Widget
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Widget Instance grid block
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('widgetInstanceGrid');
        $this->setDefaultSort('instance_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Widget_Model_Mysql4_Widget_Instance_Collection */
        $collection = Mage::getModel('widget/widget_instance')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('instance_id', array(
            'header'    => Mage::helper('widget')->__('Widget ID'),
            'align'     => 'left',
            'index'     => 'instance_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('widget')->__('Widget Instance Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('widget')->__('Type'),
            'align'     => 'left',
            'index'     => 'instance_type',
            'type'      => 'options',
            'options'   => $this->getTypesOptionsArray()
        ));

        $this->addColumn('package_theme', array(
            'header'    => Mage::helper('widget')->__('Design Package/Theme'),
            'align'     => 'left',
            'index'     => 'package_theme',
            'type'      => 'theme',
            'with_empty' => true,
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('widget')->__('Sort Order'),
            'width'     => '100',
            'align'     => 'center',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        $widgets = array();
        $widgetsOptionsArr = Mage::getModel('widget/widget_instance')->getWidgetsOptionArray();
        foreach ($widgetsOptionsArr as $widget) {
            $widgets[$widget['value']] = $widget['label'];
        }
        return $widgets;
    }

    /**
     * Retrieve design package/theme options array
     *
     * @return array
     */
    public function getPackageThemeOptionsArray()
    {
        $packageThemeArray = array();
        $packageThemeOptions = Mage::getModel('core/design_source_design')
            ->setIsFullLabel(true)->getAllOptions(false);
        foreach ($packageThemeOptions as $item) {
            if (is_array($item['value'])) {
                foreach ($item['value'] as $valueItem) {
                    $packageThemeArray[$valueItem['value']] = $valueItem['label'];
                }
            } else {
                $packageThemeArray[$item['value']] = $item['label'];
            }
        }
        return $packageThemeArray;
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('instance_id' => $row->getId()));
    }
}
