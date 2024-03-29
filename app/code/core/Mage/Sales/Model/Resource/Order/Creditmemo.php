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
 * Flat sales order creditmemo resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Creditmemo extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_creditmemo_resource';

    /**
     * Is grid available
     *
     * @var bool
     */
    protected $_grid                         = true;

    /**
     * Flag for using of increment id
     *
     * @var bool
     */
    protected $_useIncrementId               = true;

    /**
     * Entity code for increment id (Eav entity code)
     *
     * @var string
     */
    protected $_entityTypeForIncrementId     = 'creditmemo';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/creditmemo', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Mage_Sales_Model_Resource_Order_Creditmemo
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter          = $this->getReadConnection();
        $checkedFirstname = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedLastname  = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatName       = $adapter->getConcatSql(array($checkedFirstname, $adapter->quote(' '), $checkedLastname));

        $this->addVirtualGridColumn(
            'billing_name',
            'sales/order_address',
            array('billing_address_id' => 'entity_id'),
            $concatName
        )
        ->addVirtualGridColumn(
            'order_increment_id',
            'sales/order',
            array('order_id' => 'entity_id'),
            'increment_id'
        )
        ->addVirtualGridColumn(
            'order_created_at',
            'sales/order',
            array('order_id' => 'entity_id'),
            'created_at'
        );

        return $this;
    }
}
