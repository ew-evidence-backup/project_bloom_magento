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
 * Order creditmemo configuration model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Config extends Mage_Core_Model_Config_Base
{
    protected $_totalModels = null;

    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order_creditmemo'));
    }

    /**
     * Retrieve invoice total calculation models
     *
     * @return array
     */
    public function getTotalModels()
    {
        if (is_null($this->_totalModels)) {
            $this->_totalModels = array();
            $totalsConfig = $this->getNode('totals');
            foreach ($totalsConfig->children() as $totalCode=>$totalConfig) {
                $class = $totalConfig->getClassName();
                if ($class && ($model = Mage::getModel($class))) {
                    $this->_totalModels[] = $model;
                }
            }
        }
        return $this->_totalModels;
    }
}
