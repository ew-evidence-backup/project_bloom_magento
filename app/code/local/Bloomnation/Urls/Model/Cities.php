<?php

class Bloomnation_Urls_Model_Cities extends Varien_Object {
    public $cities;

    /* Load cities
     *
     * @param int $citiesPerPage
     * @param int $pageNum
     *
     * @return array $cities
     */
    public function loadCities($citiesPerPage = 100, $pageNum = 0) {
        $limitFrom = $citiesPerPage * $pageNum;
        $limitTo = $citiesPerPage;    
        // Compare against city name only
        $query = "SELECT primary_city, state FROM zip_codes_database GROUP BY primary_city, state ORDER BY state ASC LIMIT $limitFrom, $limitTo";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
        // Create links
        foreach($result as $index=>$city) {
            $result[$index]['link'] = '/'.strtolower(str_replace(' ','-',$city['primary_city'])).'-'.strtolower($city['state']).'-flower-delivery.html';
        }
        // Set data
        $this->setCities($result);
    }
    
    /* Get maximum pages number
     *
     * @param int $citiesPerPage
     *
     * @return int $maxNum
     */
    public function getMaxPageNumber($citiesPerPage) {
        $query = "SELECT COUNT(primary_city) FROM zip_codes_database GROUP BY primary_city, state";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);
        $result = sizeof($result);
        $result = ceil($result / $citiesPerPage);
        
        return $result;
    }
    
    /* Get states list from database
     *
     * no params
     *
     * @return array $states
     */
    public function getStates() {
        // Execute DB query
        $query = "SELECT DISTINCT state FROM zip_codes_database ORDER BY state ASC";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);    
        $stateArr = array_unique($result);
        
        return $stateArr;
    }
    
    /* Get cities by state
     *
     * @param string  $state -- state code
     *
     * @return array $cities
     */
    public function getCitiesByState($state) {
        $state = addslashes($state);
        
        // Execute DB query
        $query = "SELECT DISTINCT primary_city
                            FROM zip_codes_database
                            WHERE state='$state'
                            ORDER BY primary_city ASC";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);    
        $stateArr = array_unique($result);
        
        return $stateArr;        
    }
    
    /* Get florists by city
     *
     * @param string $city
     * @param string $state -- state
     * 
     * @return array $florists
     */
    function getFloristByCity($city, $state) {
        $floristsArr = array();
        foreach($this->getCityZips($city, $state) as $zip) {
            $query = "SELECT DISTINCT vendor_id
                                FROM udropship_vendor
                                WHERE zip=$zip";
            $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);    
            $floristsArr = array_merge($floristsArr, array_unique($result));            
        }
        return $floristsArr;
    }
    
    /* Get city's zipcodes
     *
     * @param string $city -- city
     * @param string $state -- state
     *
     * @return array $zipcodes
     */
    function getCityZips($city, $state) {
        $city = addslashes($city);
        $state = addslashes($state);
        // Execute DB query
        $query = "SELECT zip FROM zip_codes_database WHERE  primary_city = '$city' AND state='$state'";

        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);    
        $zipArr = array_unique($result);
        
        return $zipArr;
    }
    
    /* Get products number from a zipcode
     *
     * @param array $zipArr
     *
     * @return int $result
     */
    function getProductNumByZip($zipArr) {
        $zip = implode(',',$zipArr);
        
        // Execute DB query
        $query = "SELECT DISTINCT id
                        FROM impl_product_zip
                        WHERE zip IN($zip)";

        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);
        $result = sizeof($result);
        
        return $result;
    }
}
