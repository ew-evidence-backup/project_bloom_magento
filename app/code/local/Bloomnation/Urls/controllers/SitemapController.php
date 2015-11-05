<?php
set_time_limit(0);

class Bloomnation_Urls_SitemapController extends Mage_Core_Controller_Front_Action {
    
    public function indexAction() {
        $pathToSitemap = Mage::getBaseDir().'/var/tmp/sitemap-master.xml';
        @unlink($pathToSitemap); // Remove previois sitemap
        
        //file_put_contents($pathToSitemap,
        //    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',FILE_APPEND);
        
        // Get all categories to generate URL for
        $catArray = array(8,22,23);
        $catModelArray = array();
        foreach($catArray as $i=>$cat) {
            foreach(explode(',',Mage::getModel('catalog/category')->load($cat)->getChildren()) as $child) {
                $catModelArray[$i][] = Mage::getModel('catalog/category')->load($child);
            }
        }

        // Select cities by state
        $query = "SELECT primary_city, state FROM zip_codes_database GROUP BY primary_city, state ORDER BY state ASC";
        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
        // Get city specific URLS
        foreach($result as $res) {
            Mage::unregister('bloom_nations_zipcode_info');
            Mage::register('bloom_nations_zipcode_info',$res['primary_city'].', '.$res['state']);
            foreach($catModelArray as $index) {
                foreach($index as $catModel) {
                    file_put_contents($pathToSitemap,
                    '<url>'.
                        '<loc>'.preg_replace('/\?zipcode=\d+/','',$catModel->getUrl()).'</loc>'.
                        '<lastmod>'.date('Y-m-d').'</lastmod>'.
                        '<changefreq>daily</changefreq>'.
                        '<priority>1.0</priority>'.
                    '</url>'."\n",FILE_APPEND);
                }
            }
        }
        // Get florist URLs
        foreach(explode(',',Mage::getModel('catalog/category')->load(20)->getChildren()) as $child) {
            $catModel = Mage::getModel('catalog/category')->load($child);
            file_put_contents($pathToSitemap,
            '<url>'.
                '<loc>'.preg_replace('/\?zipcode=\d+/','',$catModel->getUrl()).'</loc>'.
                '<lastmod>'.date('Y-m-d').'</lastmod>'. 
                '<changefreq>daily</changefreq>'.
                '<priority>1.0</priority>'.
            '</url>'."\n",FILE_APPEND);        
        }
        
        // Get product URLs
        $query = "SELECT entity_id FROM catalog_product_entity WHERE sku IS NOT null ORDER BY entity_id DESC";

        $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($query);

        foreach($result as $prod) {
            $_p = Mage::getModel('catalog/product')->load($prod);
            if(1 == $_p->getStatus()) {
                file_put_contents($pathToSitemap,
                    '<url>'.
                        '<loc>'.$_p->getProductUrl().'</loc>'.
                        '<lastmod>'.date('Y-m-d').'</lastmod>'.
                        '<changefreq>daily</changefreq>'.
                        '<priority>0.5</priority>'.
                    '</url>'."\n",FILE_APPEND);
            }
        }
        
        //file_put_contents($pathToSitemap,
        //    '</urlset>',FILE_APPEND);
        
        die;
    }
    
}

?>