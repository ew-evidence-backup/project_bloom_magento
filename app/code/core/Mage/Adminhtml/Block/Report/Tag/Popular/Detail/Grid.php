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
 * Adminhtml tags detail for product report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Popular_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Report_Tag_Popular_Detail_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Reports_Model_Resource_Tag_Customer_Collection */
        $collection = Mage::getResourceModel('reports/tag_customer_collection');
        $collection->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
                ->addTagFilter($this->getRequest()->getParam('id'))
                ->addDescOrder()
                ->addProductName();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Form columns for the grid
     *
     * @return Mage_Adminhtml_Block_Report_Tag_Popular_Detail_Grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('firstname', array(
            'header'    =>Mage::helper('reports')->__('First Name'),
            'sortable'  => false,
            'index'     =>'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    =>Mage::helper('reports')->__('Last Name'),
            'sortable'  => false,
            'index'     =>'lastname'
        ));

        $this->addColumn('product', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'sortable'  => false,
            'index'     =>'product'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('added_in', array(
                'header'    => Mage::helper('reports')->__('Submitted In'),
                'sortable'  => false,
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true
            ));
        }

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportTagDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportTagDetailExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}
