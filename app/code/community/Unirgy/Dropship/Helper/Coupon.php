<?php
class Unirgy_Dropship_Helper_Coupon extends Mage_Core_Helper_Abstract
{
    public function genVendorCoupon($data)
    {
        if(empty($data['s']) || !base64_decode($data['s']) || !is_array(unserialize(base64_decode($data['s']))) )
        {
            Mage::getSingleton('udropship/session')->addError($this->__('Invalid product data.'));
            return ;
        }
        $skus = $this->checkVendorProducts(unserialize(base64_decode($data['s'])));

        // set length of coupon code
        /** @var Mage_SalesRule_Model_Coupon_Codegenerator $generator */
        $generator = Mage::getModel('salesrule/coupon_codegenerator')
            ->setLength(4);
        /** @var Mage_SalesRule_Model_Rule_Condition_Product $conditionProduct */
        $conditionProduct = Mage::getModel('salesrule/rule_condition_product')
            ->setType('salesrule/rule_condition_product')
            ->setAttribute('sku')
            ->setOperator('()')
            ->setValue($skus);
        /** @var Mage_SalesRule_Model_Rule_Condition_Product_Found $conditionProductFound */
        $conditionProductFound = Mage::getModel('salesrule/rule_condition_product_found')
            ->setConditions(array($conditionProduct));
        /** @var Mage_SalesRule_Model_Rule_Condition_Combine $condition */
        $condition = Mage::getModel('salesrule/rule_condition_combine')
            ->setConditions(array($conditionProductFound));

        /** @var Mage_SalesRule_Model_Coupon $coupon */
        $coupon = Mage::getModel('salesrule/coupon');
        $customerGroups = Mage::getModel('customer/group')->getCollection()->getAllIds();
        $vendor = Mage::getSingleton('udropship/session')->getVendor();
// create rule
        /** @var Mage_SalesRule_Model_Rule $rule */
        $rule = Mage::getModel('salesrule/rule');
        $rule->setName($data['name'])
            ->setDescription($vendor->getId().'-'.$vendor->getVendorName().' '.$data['name'].' @ '.date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())))
            ->setFromDate($data['from_date'])
            ->setToDate($data['to_date'])
            ->setCustomerGroupIds($customerGroups)
            ->setIsActive($data['is_active'])
            ->setConditionsSerialized(serialize($condition->asArray()))
//->setActionsSerialized
//->setStopRulesProcessing
            ->setIsAdvanced(1)
            ->setSimpleAction($data['simple_action'])
            ->setDiscountAmount($data['discount_amount'])
            ->setDiscountQty($data['discount_qty'])
            //->setDiscountStep
            ->setStopRulesProcessing(1)
            ->setIsRss(0)
            ->setWebsiteIds(array(1))
            ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->setConditions($condition)
            ->save();
// create coupon
        $coupon->setId(null)
            ->setRuleId($rule->getRuleId())
            ->setCode($data['coupon_code'].rand())
            ->setUsageLimit($data['uses_per_coupon'])
            ->setUsesPerCustomer($data['uses_per_customer'])
//->setUsagePerCustomer
//->setTimesUsed
//->setExpirationDate
            ->setIsPrimary(1)
            ->setCreatedAt(time())
            ->setType(1)
            ->save();
            if($coupon->getId())
            {
                $vc = Mage::getModel('udropship/vendor_coupon');
                $vc->setVendorId($vendor->getId())
                    ->setCouponId($coupon->getId());
                $vc->save();
                Mage::getSingleton('udropship/session')->addSuccess($this->__('Coupon created successfully .'));
                //return $coupon->getId();
            }
    }
    public function checkVendorProducts($skus)
    {
        $pid = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('sku',array('in'=>$skus))
                ->getAllIds();
        $vid = Mage::getSingleton('udropship/session')->getVendor()->getAssociatedProducts($pid);
        $nid = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id',array('in'=>array_keys($vid)));
        foreach($nid as $v)
        {
            $arr[] = $v->getSku();
        }
        return $arr;
    }
}