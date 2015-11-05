<?php

class Bloomnation_Urls_Model_Rewrite_Core_Url extends Mage_Core_Model_Url {
    
    public function getUrl($routePath = null, $routeParams = null) {
        $url = parent::getUrl($routePath, $routeParams);
	
        if($subD = Mage::registry('current_subdomain')) {
            $urlParts = parse_url($url);
            $urlHostParts = explode('.', $urlParts['host']);
		// Custom case for dev server
		if('dev.bloomnation.com' == $urlParts['host']) {
		 $urlParts['host'] = 'lapremier.dev.bloomnation.com';
		} else {
            $urlHostParts[0] = $subD;
            $urlParts['host'] = implode('.', $urlHostParts);
		}

            $url=(isset($urlParts["scheme"])?$urlParts["scheme"]."://":"").
                 (isset($urlParts["user"])?$urlParts["user"].":":"").
                 (isset($urlParts["pass"])?$urlParts["pass"]."@":"").
                 (isset($urlParts["host"])?$urlParts["host"]:"").
                 (isset($urlParts["port"])?":".$urlParts["port"]:"").
                 (isset($urlParts["path"])?$urlParts["path"]:"").
                 (isset($urlParts["query"])?"?".$urlParts["query"]:"").
                 (isset($urlParts["fragment"])?"#".$urlParts["fragment"]:"");
        }
        
        return $url;
    }
}
