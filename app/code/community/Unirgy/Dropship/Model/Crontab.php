<?php

class Unirgy_Dropship_Model_Crontab extends Varien_Object {
    
    public function vendorEmail() {
        // Get list of three day old florists
        foreach($this->getThreeDayFlorists() as $vendor) {
            $this->sendFirstEmail($vendor);
        }
        // Get list of three week old florists
        foreach($this->getThreeWeekFlorists() as $vendor) {
            $this->sendSecondEmail($vendor['email']);
        }
        
    }
    
    /* Get three day old vendors
     * 
     * no params
     *
     * return array $result
     */
    public function getThreeDayFlorists() {
        $date = date('Y-m-d',strtotime('-3 days'));
        
        $query = "SELECT email
                            FROM udropship_vendor
                            WHERE created_at
                            LIKE '$date%'";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);

        return $result;
    }
    
    /* Get three week old vendors
     *
     * @return void
     */
    public function getThreeWeekFlorists() {
        $date = date('Y-m-d',strtotime('-15 days'));
        
        $query = "
            SELECT uv.email as email, COUNT(uvp.product_id) as product_count FROM `udropship_vendor` uv 
            LEFT JOIN udropship_vendor_product uvp 
            ON uv.vendor_id=uvp.vendor_id
            WHERE uv.created_at LIKE '$date%'
            GROUP BY uv.email
            HAVING product_count < 15
            ORDER BY uv.created_at DESC
        ";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);

        return $result;        
    }
    
    
    /* First email
     *
     * @return void
     */
    public function sendFirstEmail($to) {
        // Send greeting email
      $emailTemplate  = Mage::getModel('core/email_template')
                              ->loadDefault('udropship_vendor_threeday');
       //Create an array of variables to assign to template
       $emailTemplateVariables = array();
       $emailTemplateVariables['vendorname'] = 'Test';
       $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
       $emailTemplate->setSenderName('David Daneshgar');
       $emailTemplate->setSenderEmail('david@bloomnation.com');
       try {
            if(!$emailTemplate->send($to, 'David Daneshgar', $emailTemplateVariables)) {
                Mage::getSingleton('udropship/session')->addError('Error sending greeting email');
            }
       } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'mail.log');
       }
    }
    
    /* Second email
     *
     *
     * @return void
     */
    public function sendSecondEmail($to) {
        // Send greeting email
      $emailTemplate  = Mage::getModel('core/email_template')
                              ->loadDefault('udropship_vendor_twoweek');
       //Create an array of variables to assign to template
       $emailTemplateVariables = array();
       $emailTemplateVariables['vendorname'] = 'Test';
       $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
       $emailTemplate->setSenderName('David Daneshgar');
       $emailTemplate->setSenderEmail('david@bloomnation.com');
       try {
            if(!$emailTemplate->send($to, 'David Daneshgar', $emailTemplateVariables)) {
                Mage::getSingleton('udropship/session')->addError('Error sending greeting email');
            }
       } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'mail.log');
       }
    }
    
}

?>