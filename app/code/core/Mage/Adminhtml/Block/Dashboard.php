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

class Mage_Adminhtml_Block_Dashboard extends Mage_Adminhtml_Block_Template
{
    protected $_locale;

    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/index.phtml');

    }

    protected function _prepareLayout()
    {
        $this->setChild('lastOrders',
                $this->getLayout()->createBlock('adminhtml/dashboard_orders_grid')
        );

        $this->setChild('totals',
                $this->getLayout()->createBlock('adminhtml/dashboard_totals')
        );

        $this->setChild('sales',
                $this->getLayout()->createBlock('adminhtml/dashboard_sales')
        );

        $this->setChild('lastSearches',
                $this->getLayout()->createBlock('adminhtml/dashboard_searches_last')
        );

        $this->setChild('topSearches',
                $this->getLayout()->createBlock('adminhtml/dashboard_searches_top')
        );

        if (Mage::getStoreConfig(self::XML_PATH_ENABLE_CHARTS)) {
            $block = $this->getLayout()->createBlock('adminhtml/dashboard_diagrams');
        } else {
            $block = $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('dashboard/graph/disabled.phtml')
                ->setConfigUrl($this->getUrl('adminhtml/system_config/edit', array('section'=>'admin')));
        }
        $this->setChild('diagrams', $block);

        $this->setChild('grids',
                $this->getLayout()->createBlock('adminhtml/dashboard_grids')
        );

        parent::_prepareLayout();
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current'=>true, 'period'=>null));
    }
}
