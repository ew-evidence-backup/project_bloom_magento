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
 * Adminhtml sales order's status namagement block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Status extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_headerText = Mage::helper('sales')->__('Order Statuses');
        $this->_addButtonLabel = Mage::helper('sales')->__('Create New Status');
        $this->_addButton('assign', array(
            'label'     => Mage::helper('sales')->__('Assign Status to State'),
            'onclick'   => 'setLocation(\'' . $this->getAssignUrl() .'\')',
            'class'     => 'add',
        ));
        parent::__construct();
    }

    /**
     * Create url getter
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_status/new');
    }

    /**
     * Assign url getter
     *
     * @return string
     */
    public function getAssignUrl()
    {
        return $this->getUrl('*/sales_order_status/assign');
    }
}
