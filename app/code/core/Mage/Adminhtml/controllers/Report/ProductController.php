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
 * Product reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Report_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init
     *
     * @return Mage_Adminhtml_Report_ProductController
     */
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if(!$act)
            $act = 'default';
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('reports')->__('Products'), Mage::helper('reports')->__('Products'));
        return $this;
    }

    /**
     * Bestsellers
     *
     * @deprecated after 1.4.0.1
     */
    public function orderedAction()
    {
        return $this->_forward('bestsellers', 'report_sales');
    }

    /**
     * Export products bestsellers report to CSV format
     *
     * @deprecated after 1.4.0.1
     */
    public function exportOrderedCsvAction()
    {
        return $this->_forward('exportBestsellersCsv', 'report_sales');
    }

    /**
     * Export products bestsellers report to XML format
     *
     * @deprecated after 1.4.0.1
     */
    public function exportOrderedExcelAction()
    {
        return $this->_forward('exportBestsellersExcel', 'report_sales');
    }

    /**
     * Sold Products Report Action
     *
     */
    public function soldAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Products Ordered'));

        $this->_initAction()
            ->_setActiveMenu('report/product/sold')
            ->_addBreadcrumb(Mage::helper('reports')->__('Products Ordered'), Mage::helper('reports')->__('Products Ordered'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_sold'))
            ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     *
     */
    public function exportSoldCsvAction()
    {
        $fileName   = 'products_ordered.csv';
        $content    = $this->getLayout()
            ->createBlock('adminhtml/report_product_sold_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sold Products report to XML format action
     *
     */
    public function exportSoldExcelAction()
    {
        $fileName   = 'products_ordered.xml';
        $content    = $this->getLayout()
            ->createBlock('adminhtml/report_product_sold_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Most viewed products
     *
     */
    public function viewedAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Most Viewed'));

        $this->_initAction()
            ->_setActiveMenu('report/product/viewed')
            ->_addBreadcrumb(Mage::helper('reports')->__('Most Viewed'), Mage::helper('reports')->__('Most Viewed'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_viewed'))
            ->renderLayout();
    }

    /**
     * Export products most viewed report to CSV format
     *
     */
    public function exportViewedCsvAction()
    {
        $fileName   = 'products_mostviewed.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products most viewed report to XML format
     *
     */
    public function exportViewedExcelAction()
    {
        $fileName   = 'products_mostviewed.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_viewed_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Low stock action
     *
     */
    public function lowstockAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Low Stock'));

        $this->_initAction()
            ->_setActiveMenu('report/product/lowstock')
            ->_addBreadcrumb(Mage::helper('reports')->__('Low Stock'), Mage::helper('reports')->__('Low Stock'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_lowstock'))
            ->renderLayout();
    }

    /**
     * Export low stock products report to CSV format
     *
     */
    public function exportLowstockCsvAction()
    {
        $fileName   = 'products_lowstock.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_lowstock_grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export low stock products report to XML format
     *
     */
    public function exportLowstockExcelAction()
    {
        $fileName   = 'products_lowstock.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_lowstock_grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Downloads action
     *
     */
    public function downloadsAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Products'))
             ->_title($this->__('Downloads'));

        $this->_initAction()
            ->_setActiveMenu('report/product/downloads')
            ->_addBreadcrumb(Mage::helper('reports')->__('Downloads'), Mage::helper('reports')->__('Downloads'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product_downloads'))
            ->renderLayout();
    }

    /**
     * Export products downloads report to CSV format
     *
     */
    public function exportDownloadsCsvAction()
    {
        $fileName   = 'products_downloads.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_downloads_grid')
            ->setSaveParametersInSession(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export products downloads report to XLS format
     *
     */
    public function exportDownloadsExcelAction()
    {
        $fileName   = 'products_downloads.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/report_product_downloads_grid')
            ->setSaveParametersInSession(true)
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'viewed':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/viewed');
                break;
            case 'sold':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/sold');
                break;
            case 'lowstock':
                return Mage::getSingleton('admin/session')->isAllowed('report/products/lowstock');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/products');
                break;
        }
    }
}
