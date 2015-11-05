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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Mage_Reports_Model_Report extends Mage_Core_Model_Abstract
{
    protected $_reportModel;

    public function initCollection($modelClass)
    {
        $this->_reportModel = Mage::getResourceModel($modelClass);

        return $this;
    }

    public function getReportFull($from, $to)
    {
        return $this->_reportModel
            ->setDateRange($from, $to)
            ->setPageSize(false)
            ->setStoreIds($this->getStoreIds());
    }

    public function getReport($from, $to)
    {
        return $this->_reportModel
            ->setDateRange($from, $to)
            ->setPageSize($this->getPageSize())
            ->setStoreIds($this->getStoreIds());
    }
}
