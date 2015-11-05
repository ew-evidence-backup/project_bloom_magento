<?php

require_once('lib/Simplepie/autoloader.php');

class Unirgy_Dropship_Block_News extends Mage_Core_Block_Template {

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 3600
        ));
    }
    
    public function getCacheKeyInfo() {
        $cache = array();
        
        $cache['tname'] = 'Bloomnation_News_Items_Block_Signed';
        $cache['module'] = 'Dashboard';
        
        return $cache;
    }    

    /* Get items from blog feed
     *
     * no params
     *
     * @return Simple Pie Feed items
     */
    public function getNews() {
        $feed = new SimplePie();
        $feed->set_feed_url('http://feeds.rapidfeeds.com/54082/');
        $success = $feed->init();

        if(!$success) {
            throw new Exception('couldnt init blog feed');
        }
        
        return $feed->get_items();
    }
    
    
}