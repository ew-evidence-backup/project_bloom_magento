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

class Unirgy_DropshipMulti_Model_Decision_LeastVendorsLowestCost
    extends Unirgy_DropshipMulti_Model_Decision_MultiVendor
{
    public function apply($items)
    {
        $this->collectStockLevels($items);
        $this->beforeApply($items);

        $stock = $this->getStockToApply();
        if (!$stock) {
            return $this;
        }
        // flatten products array to allow simple iteration
        usort($stock, array($this, 'flattenCallback'));
        foreach ($stock as &$vendors) {
            usort($vendors, array($this, 'flattenCallback'));
        }
        unset($vendors);
#echo "<pre>"; print_r($stock); echo "</pre>"; exit;
        $combinations = array();
        $vis = array();
        $pl = sizeof($stock);
        // 10000 should be enough to iterate through all combinations
        for ($safeguard=0; $safeguard<10000; $safeguard++) {
            // collect combination from current vendor ids
            $combination = array();
            $uniqueVendors = array();
            $totalCost = 0;
            foreach ($stock as $pi=>$vendors) {
                // reset vendor ids
                if (empty($vis[$pi])) {
                    $vis[$pi] = 0;
                }
                // add vendor to combination
                $v = $vendors[$vis[$pi]];
                if (empty($v['status'])) {
                    continue;
                }
                $combination['products'][$pi] = array(
                    'p' => $v['product_id'],
                    'v' => $v['vendor_id'],
                    'c' => $v['vendor_cost'],
                );
                // update combination totals
                $uniqueVendors[$v['vendor_id']] = @$v['priority'];
                $totalCost += $v['vendor_cost'];
            }

            // if all products are in stock for this combination
            if (!empty($combination['products']) && sizeof($combination['products'])==$pl) {
                // add combination to list
                $combination['num_vendors'] = sizeof($uniqueVendors);
                $combination['total_priority'] = array_sum($uniqueVendors);
                $combination['total_cost'] = $totalCost;
                $combinations[] = $combination;
            }

            // find next vendor ids
            foreach ($vis as $pi=>&$vi) {
                $vi++;
                if ($vi<sizeof($stock[$pi])) {
                    break;
                }
                $vi = 0;
                if ($pi==$pl-1) {
                    break 2;
                }
            }
            unset($vi);
        }

        // no combinations found (with all products in stock)
        if (empty($combinations)) {
            return $this;
        }

        // find the most efficient combination (after sorting it is the first)
        usort($combinations, array($this, 'sortCombinationsCallback'));
        $combination = $combinations[0]['products'];

        $iHlp = Mage::helper('udropship/item');
        // set optimized vendor ids to cart items
        foreach ($items as $item) {
            if ($iHlp->getStickedVendorIdOption($item)) continue;
            foreach ($combination as $v) {
                if ($item->getProductId()==$v['p']) {
                    $item->setUdropshipVendor($v['v']);
                    $item->setCost($v['c']);
                    $item->setBaseCost($v['c']);
                    break;
                }
            }
            if ($item->getHasChildren()) {
                $children = $item->getChildrenItems() ? $item->getChildrenItems() : $item->getChildren();
                foreach ($children as $child) {
                    $product = $child->getProduct();
		            $pId = $child->getProductId();
		            if (!$product || !$product->hasUdropshipVendor()) {
		                // if not available, load full product info to get product vendor
		                $product = Mage::getModel('catalog/product')->load($pId);
		            }
                    if ($product->getTypeInstance()->isVirtual()) {
                        continue;
                    }
                    foreach ($combination as $v) {
                        if ($child->getProductId()==$v['p']) {
                            $item->setUdropshipVendor($v['v']);
                            $child->setUdropshipVendor($v['v']);
                            $child->setCost($v['c']);
                            $child->setBaseCost($v['c']);
                            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                                $item->setCost($v['c']);
                                $item->setBaseCost($v['c']);
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function flattenCallback($v1, $v2)
    {
        return 0;
    }

    public function sortCombinationsCallback($c1, $c2)
    {
        if ($c1['num_vendors']<$c2['num_vendors']) {
            return -1;
        } elseif ($c1['num_vendors']>$c2['num_vendors']) {
            return 1;
        }
        if ($c1['total_cost']<$c2['total_cost']) {
            return -1;
        } elseif ($c1['total_cost']>$c2['total_cost']) {
            return 1;
        }
        if ($c1['total_priority']<$c2['total_priority']) {
            return -1;
        } elseif ($c1['total_priority']>$c2['total_priority']) {
            return 1;
        }
        return 0;
    }

    //public function calculateTotalCost
}
