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
 * @package    Unirgy_DropshipMulti
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

class Unirgy_DropshipMulti_Model_Decision_MultiVendor
    extends Unirgy_Dropship_Model_Vendor_Decision_Abstract
{
    public function beforeApply($items)
    {
        $this->setStockToApply($this->getStockResult());
        return $this;
    }
    public function collectStockLevels($items, $options=array())
    {
        $availability = Mage::getSingleton('udmulti/stock_availability');
        $availability->collectStockLevels($items, $options);
        $this->setStockResult($availability->getStockResult());
        return $this;
    }
}