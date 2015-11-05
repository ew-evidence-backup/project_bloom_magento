<?php
class Impl_Custom_IndexController extends Mage_Core_Controller_Front_Action
{
	public function ajax_add_zipAction()
  {
		$zipcode = $this->getRequest()->getParam('zipcode', null);
		
		// Get contry/City information
		$json = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $zipcode . '&sensor=true&language=en');
		$data = json_decode($json);

		if($data->status == 'OK' && $zipcode != '00000')
		{
			if(isset($data->results[0]) && !empty($data->results[0]))
			{
				$address = $data->results[0]->formatted_address;
				$split = split(", ", $address);
				$address = $split[0] . ', ' . $split[1];
				setcookie("bloom_nations_zipcode_info", $address, 0, '/');
			}
		}
		
		if($zipcode == '00000')
		{
			setcookie("bloom_nations_zipcode_info", 'All Locations, 00000', 0, '/');
		}
		
		setcookie("bloom_nations_zipcode", $zipcode, 0, '/');
		echo json_encode(array('status' => 'success', 'message' => '<span style="color: green;" class="zippopup-green">You have successfully added zip code. Reloading...</span>'));
		exit;
	}
	
	public function searchAction()
	{
		$typeOfSearch = '';
		$searchInfo = array();
		$query = trim(addslashes($this->getRequest()->getParam('query')));
		$occasion = $this->getRequest()->getParam('occasion');

		$redirectUrl = false;

		// Stip these words off search
		$stripOffWords = array(
			'flowers in',
			'flower delivery in',
			'flower delivery',
			'flowers',
		);
		$query = str_ireplace($stripOffWords, '', $query);
		$query = trim($query);

		
		if($occasion) {
			$category = Mage::getModel('catalog/category')->load($occasion);
		}

		// Detect type of search
		if(preg_match('/\d{5}/',$query)) { // Zipcode
			$typeOfSearch = 'zipcode';
		} else if (preg_match('/^(.+)\W+(\w\w)$/',$query,$matches)) { // City, STATE
			$matches = str_replace(',','',$matches);
			$searchInfo = $matches;
			$typeOfSearch = 'city_and_short_state';
		} else {
			$typeOfSearch = 'vendor_name';
		}
		
		switch($typeOfSearch) {
			case 'zipcode':
				$zipcode = $query;
				// Get zipcode information from the database
				$query = "SELECT primary_city,state FROM zip_codes_database WHERE  zip = $zipcode";
				$result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($query);    		
			
				if(sizeof($result) > 0) {		
					$city = strtolower(str_replace(' ','-',$result['primary_city']));
					$state = strtolower($result['state']);
					$redirectUrl = $city.'-'.$state.'-flower-delivery.html?zipcode='.$zipcode;
					$catname = $category->getUrlKey();
					if($occasion && $category->getId() != 8) {
						$redirectUrl = $city.'-'.$state.'/'.$catname.'-bouquets.html?zipcode='.$zipcode;
					}
					setcookie("bloom_nations_zipcode_info", $result['primary_city'].' '.$result['state'], 0, '/');
				} else {
					setcookie("bloom_nations_zipcode_info", 'Shop our catalogue', 0, '/');
					$redirectUrl = $category->getUrlPath();
				}
			break;
			case 'city_and_short_state':
				$city = strtolower(str_replace(' ','-',$searchInfo[1]));
				$state = strtolower($searchInfo[2]);
				
				$redirectUrl = $city.'-'.$state.'-flower-delivery.html';
			break;
			case 'vendor_name':
				$vendorName = htmlspecialchars_decode($query);

				$query = "SELECT city, vendor_name
							FROM udropship_vendor
							WHERE vendor_name LIKE '%".$vendorName."%'";
				$result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow($query);

				if($result) {
					$city = strtolower(str_replace(' ','-',$result['city']));
					$result['vendor_name'] = str_replace("&",'',$result['vendor_name']);
					$vendorName = strtolower(str_replace(' ','-',$result['vendor_name']));
					$vendorName = str_replace("'",'-',$vendorName);
					$vendorName = str_replace(",",'',$vendorName);
					$vendorName = str_replace("--",'-',$vendorName);
					$redirectUrl = $city.'-'.$vendorName.'.html';
				}
			break;
		}

		// Make sure no conflicts between product zipcodes in the cart when browsing different zipcodes
		Mage::helper('urls')->clearSession($zipcode);
		setcookie("bloom_nations_zipcode", $zipcode, 0, '/');
		// Not using magento _redirect because it adds trailing slash
		header('Location: /'.$redirectUrl); exit;
	}
	
	public function tooltipAction() {
		$success = false;
		$msg = '';
		$data = array();
		
		$query = $this->getRequest()->getParam('query');
		
		if(!$query) {
			$msg = 'Query is empty';
			exit(json_encode(compact('success','msg','data')));
		}
		
		$this->loadLayout('custom_index_tooltip');
		$data = $this->getLayout()->getBlock('tooltip')->setQuery($query)->toHtml();
		if(sizeof($data) > 0) {
			$success = true;
		}
		
		// Send the response
		$response = json_encode(compact('success','msg','data'));
		exit($response);
	}
}
