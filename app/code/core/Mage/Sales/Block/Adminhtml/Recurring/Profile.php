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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Adminhtml sales orders block
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'sales';
    protected $_controller = 'adminhtml_recurring_profile';

    /**
     * Set header text and remove "add" btn
     */
    public function __construct()
    {
        $this->_headerText = Mage::helper('sales')->__('Recurring Profiles (beta)');
        parent::__construct();
        $this->_removeButton('add');
    }
}
