<?php

class SMDesign_Colorswatch_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $_model;
	
	function setModel($model) {
		$this->_model = $model;
	}
	
	function getModel() {
		
		return $this->_model;
	}
	
}
?>