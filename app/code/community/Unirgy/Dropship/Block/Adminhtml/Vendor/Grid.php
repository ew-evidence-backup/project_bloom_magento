<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_Dropship
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

class Unirgy_Dropship_Block_Adminhtml_Vendor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendorGrid');
        $this->setDefaultSort('vendor_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('vendor_filter');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('udropship/vendor')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('udropship');
        $this->addColumn('vendor_id', array(
            'header'    => $hlp->__('Vendor ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'vendor_id',
            'type'      => 'number',
        ));

        $this->addColumn('vendor_name', array(
            'header'    => $hlp->__('Vendor Name'),
            'index'     => 'vendor_name',
        ));

        $this->addColumn('email', array(
            'header'    => $hlp->__('Email'),
            'index'     => 'email',
        ));

        if ($hlp->isModuleActive('ustockpo')) {
            $this->addColumn('distributor_id', array(
                'header' => Mage::helper('ustockpo')->__('Distributor'),
                'index' => 'distributor_id',
                'type' => 'options',
                'options' => Mage::getSingleton('udropship/source')->setPath('vendors')->toOptionHash(),
            ));
        }

        $this->addColumn('created_at', array(
            'header'    => $hlp->__('Date of Sign Up'),
            'index'     => 'created_at',
            'type'      => 'date',
        ));        

        $this->addColumn('login_at', array(
            'header'    => $hlp->__('Date of Log In'),
            'index'     => 'login_at',
            'type'      => 'datetime',
        ));        
        
        $this->addColumn('status', array(
            'header'    => $hlp->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('udropship/source')->setPath('vendor_statuses')->toOptionHash(),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('adminhtml')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('vendor');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('udropship')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('udropship')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('udropship')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'status' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('udropship')->__('Status'),
                         'values' => Mage::getSingleton('udropship/source')->setPath('vendor_statuses')->toOptionArray(true),
                     )
             )
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
