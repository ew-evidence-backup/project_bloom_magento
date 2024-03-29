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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Adminhtml store grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Store_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('storeGrid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('core/website')
            ->getCollection()
            ->joinGroupAndStore();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('website_title', array(
            'header'        => Mage::helper('core')->__('Website Name'),
            'align'         =>'left',
            'index'         => 'name',
            'filter_index'  => 'main_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_website'
        ));

        $this->addColumn('group_title', array(
            'header'        => Mage::helper('core')->__('Store Name'),
            'align'         =>'left',
            'index'         => 'group_title',
            'filter_index'  => 'group_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_group'
        ));

        $this->addColumn('store_title', array(
            'header'        => Mage::helper('core')->__('Store View Name'),
            'align'         =>'left',
            'index'         => 'store_title',
            'filter_index'  => 'store_table.name',
            'renderer'      => 'adminhtml/system_store_grid_render_store'
        ));

        return parent::_prepareColumns();
    }

}
