<?php
include 'var/tmp/vendor.php';

class Unirgy_Dropship_MigrateController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        exit('out of service');
        $vendor = new vendorarr();
        $vendor = $vendor->vendor;
        
        foreach($vendor as $l) {
            $lData = $l;
            
            $email = $lData['email'];
            
            $vData['florist_type'] = strtolower(str_replace(' ','_',$lData['florist_type']));
            $vData['vendor_attn'] = $lData['first_name'].' '.$lData['last_name'];
            $vData['vendor_website'] = $lData['website'];
            $vData['delivery_charge'] = $lData['delivery_fee'];
            $vData['has_same_day_delivery'] = ('Yes' == $lData['same_day']) ? 'true' : 'false';
            $vData['cut_off_time'] = trim($lData['cutoff_time']);
            $vData['v_timezone'] = trim(substr($lData['cutoff_time'], -4));
            $vData['open_at'] = $lData['operations_monday_from'];
            $vData['close_at'] = $lData['operations_monday_to'];
            $vData['fax'] = preg_replace('/\D/','',$lData['delivery_fax']);
            $vData['dozen_rose_price'] = $lData['dozen_prize'];
            $vData['facebook_page'] = $lData['facebook_url'];
            
            $vendor = Mage::getModel('udropship/vendor')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('email', $email)
                ->getFirstItem();

            foreach($vData as $key => $val) {
                if('' != $val) {
                    $vendor->setData($key, $val);
                }
            }
            
            $vendor->setTelephone(preg_replace('/\D/', '', $vendor->getTelephone()));
            
            try {
                $vendor->save();
            } catch(Exception $e) {
                echo $vendor->getEmail().': '.$e->getMessage()."<br/>";
            }
        }
        
        die;
    }
}