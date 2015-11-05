<?php

class Bloomnation_Urls_IndexController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function generatexmlAction() {
        $floristData = array();
        $cityData = array();

        foreach(Mage::getModel('urls/cities')->getStates() as $state) {
            foreach(Mage::getModel('urls/cities')->getCitiesByState($state) as $city) {
                // For by state -> city -> florist breakdown
                foreach(Mage::getModel('urls/cities')->getFloristByCity($city, $state) as $florist) {
                    $cat = Mage::helper('udropship')->getCategoryByVendor($florist);
                    if($cat) {
                        $prodCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($cat);
                        if($size = $prodCollection->getSize()) {
                            $floristData[$state][$florist] = $size;
                        }
                    }
                }
                // For by state -> city
                if($zipNumber = Mage::getModel('urls/cities')->getProductNumByZip(Mage::getModel('urls/cities')->getCityZips($city, $state))) {
                    $cityData[$state][$city] = $zipNumber;
                }
                
            }
        }
        // Sort florist data by top city
        foreach($floristData as $i=>$florArr) {
            arsort($floristData[$i]);
        }
        // Sort city products by top city
        foreach($cityData as $i=>$cityArr) {
            arsort($cityData[$i]);
        } 
        
        // Generate XML for cities
        $xml = '<citylist>'."\n";
        foreach($cityData as $state=>$city) {
            $xml .= '<state>'."\n".
                            '<name>'.$state.'</name>'."\n".
                            '<cities>';
                            
            foreach($city as $city=>$rating) {
                $xml .=     '<city>'."\n".
                                    '<name>'.$city.'</name>'."\n".
                                    '<rating>'.$rating.'</rating>'."\n".
                                '</city>';
            }
            $xml.=      '</cities>'."\n".
                        '</state>';
        }
        $xml.= '</citylist>';
        file_put_contents('ghassets/xml/citylist.xml',$xml);
        // General XML for florists
        $xml = '<floristlist>';
        foreach($floristData as $state=>$florist) {
            $xml .= '<state>'."\n".
                            '<name>'.$state.'</name>'."\n".
                            '<florists>';
            foreach($florist as $florist=>$rating) {
                $xml .=     '<florist>'."\n".
                                    '<name>'.Mage::getModel('udropship/vendor')->load($florist)->getVendorName().'</name>'."\n".
                                    '<rating>'.$rating.'</rating>'."\n".
                                    '<id>'.$florist.'</id>'."\n".
                                '</florist>';
            }
            $xml.=      '</florists>'."\n".
                        '</state>';
        }
        $xml.='</floristlist>';
        file_put_contents('ghassets/xml/floristlist.xml', $xml);
        die;
        
        $this->loadLayout();
        $this->renderLayout();        
    }
    
    public function topcityAction() {
        $cities = simplexml_load_file('ghassets/xml/citylist.xml');
        
        $this->loadLayout();
        $this->getLayout()->getBlock('topcity')->setCities($cities);
        $this->renderLayout();
    }

    public function topfloristAction() {
        $florists = simplexml_load_file('ghassets/xml/floristlist.xml');

        $this->loadLayout();
        $this->getLayout()->getBlock('topflorist')->setFlorists($florists);
        $this->renderLayout();
    }
    
}

?>