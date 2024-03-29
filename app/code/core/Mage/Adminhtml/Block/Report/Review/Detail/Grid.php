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
 * Adminhtml report reviews product grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Review_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('reviews_grid');
    }

    protected function _prepareCollection()
    {

        //$collection = Mage::getModel('review/review')->getProductCollection();

        //$collection->getSelect()
        //    ->where('rt.entity_pk_value='.(int)$this->getRequest()->getParam('id'));

        //$collection->getEntity()->setStore(0);

        $collection = Mage::getResourceModel('reports/review_collection')
            ->addProductFilter((int)$this->getRequest()->getParam('id'));

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('nickname', array(
            'header'    =>Mage::helper('reports')->__('Customer'),
            'width'     =>'100px',
            'index'     =>'nickname'
        ));

        $this->addColumn('title', array(
            'header'    =>Mage::helper('reports')->__('Title'),
            'width'     =>'150px',
            'index'     =>'title'
        ));

        $this->addColumn('detail', array(
            'header'    =>Mage::helper('reports')->__('Detail'),
            'index'     =>'detail'
        ));

        $this->addColumn('created_at', array(
            'header'    =>Mage::helper('reports')->__('Created At'),
            'index'     =>'created_at',
            'width'     =>'200px',
            'type'      =>'datetime'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}

