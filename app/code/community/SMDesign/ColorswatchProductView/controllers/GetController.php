<?php
class SMDesign_ColorswatchProductView_GetController extends Mage_Core_Controller_Front_Action {
	
	function mainImageAction() {
		
		$selection = Mage::helper('core')->jsonDecode($this->getRequest()->getParam('selection', '[]'));
		$attributeId = $this->getRequest()->getParam('attribute_id');
		$optionId = $this->getRequest()->getParam('option_id');
		$productId = $this->getRequest()->getParam('product_id');
    $imageSelector = $this->getRequest()->getParam('image_selector', '.product-img-box .product-image img');
    
		$_product = Mage::getModel('catalog/product')->load($productId);
		if (!$_product->getId()) {
			$this->_forward('noRoute');
			return;
		}

		$selectedAttributeCode = $_product->getTypeInstance(true)->getAttributeById($attributeId, $_product)->getAttributeCode();

		$colorswatch = Mage::getModel('colorswatch/product_swatch')->setProduct($_product);
		$allProducts = $colorswatch->getAllowProducts();

		foreach ($allProducts as $product) {
		    if ($product->isSaleable() && $product->getIsInStock()) {
		    	if (Mage::getModel('colorswatch/attribute_settings')->getConfig($attributeId, 'allow_attribute_to_change_main_image') == 1 ) {
		    		if ($product->getData($selectedAttributeCode) == $optionId) {
		    			 $products[] = $product;
		    		}
		    	} else {
		    		 $products[] = $product;
		    	}
		    }
		}

		$selected = array();
		foreach ($selection as $val) {
			if ($val['selected'] == 1 && Mage::getModel('colorswatch/attribute_settings')->getConfig($val['attribute_id'], 'allow_attribute_to_change_main_image') == 1) {
				$selected[$val['attribute_id']] = $val['option_id'];
			}
		}

		$allAvialableAttributeCode = $colorswatch->getAllAttributeCodes();
		foreach ($colorswatch->getAllAttributeIds() as $aKey=>$aId) {
			
			if (!isset($selected[$aId]) && Mage::getModel('colorswatch/attribute_settings')->getConfig($aId, 'allow_attribute_to_change_main_image') == 1) {
				$options = $colorswatch->getAttributeById($aId)->getColorswatchOptions()->getData();
				$optionCount = count($options);
				$optionIndex = 0;
				
				while ($optionIndex < $optionCount) {
					$option = $options[$optionIndex];
					
					if ($this->productExsist($products, $allAvialableAttributeCode[$aKey], $option['option_id'])) {
						$selected[$aId] = $option['option_id'];
						$optionIndex = count($options);
					}
					$optionIndex++;
				}
			}
			

			if (isset($selected[$aId])) {
				foreach ($products as $key=>$simpleProduct) {
						if ($simpleProduct->getData($allAvialableAttributeCode[$aKey]) != $selected[$aId]) {
							unset($products[$key]);
						}
			  }
			}
			
		}
		
		if (count($selected) == 0) { // not have attribut who is allowed to change image
		  echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);";
		  echo "  //not have allowed attribute to change image.\n";
		  exit();
		}
		
		
		/* always need big image becose image need to zoom */
		$imgElementWidth = null;
		$imgElementHeight = null;
		
		if (Mage::getStoreConfig('smdesign_colorswatch/zoom/image_size_type') == 2) {
			$imgElementWidth = Mage::getStoreConfig('smdesign_colorswatch/zoom/container_width')*Mage::getStoreConfig('smdesign_colorswatch/zoom/zoom_ratio');
			$imgElementHeight = Mage::getStoreConfig('smdesign_colorswatch/zoom/container_height')*Mage::getStoreConfig('smdesign_colorswatch/zoom/zoom_ratio');
		}
		
		if (!($_product->getImage() != 'no_selection' && $_product->getImage())) {
		/* use detected dimension from js */
		  if (Mage::getStoreConfig('smdesign_colorswatch/zoom/image_size_type') != 2) {
  		  $imgElementWidth = 	$this->getRequest()->getParam('img_width', null);	
  		  $imgElementHeight = 	$this->getRequest()->getParam('img_height', null);
		  }
  		echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);\n";
		}
		
		$images = array();
		
		if (count($products) > 0) {
			
			foreach ($products as $simpleProduct) {
				if (count($images) == 0) {
	        $simpleProduct->load();
	        $simpleProductImages = $simpleProduct->getMediaGalleryImages();

	        if (count($simpleProductImages)) {
	          foreach ($simpleProductImages as $_image) {
	            
              if (Mage::getStoreConfig('smdesign_colorswatch/zoom/image_size_type') && Mage::getStoreConfig('smdesign_colorswatch/zoom/image_size_type') != 2) { // == 2 calculated fixed size // 1 dynamic size
              	$newHeight = null;
                $newWidth = null;             	
//              						  $cImage = Mage::helper('catalog/image')->init($simpleProduct, 'image');
//              		
//              			
//              		          $width = $cImage->getOriginalWidth();
//              		          $height = $cImage->getOriginalHeight();
//              			
//              		          $wRatio = $width/$imgElementWidth;
//              		          $hRatio = $height/$imgElementHeight;
//              		          $ratio = max($wRatio, $hRatio);
//              		          
//              		          if ($wRatio > $hRatio) {
//              		          	$newWidth = $width;
//              		          	$newHeight = ($width * $imgElementHeight / $imgElementWidth );
//              		          } else {
//              		          	$newHeight = $height;
//              		          	$newWidth = ($height * $imgElementWidth / $imgElementHeight );
//              		          }
              } else {
                $newHeight = $imgElementHeight;
                $newWidth = $imgElementWidth;
              }
   
              	          	
	          	
	          	if ($simpleProduct->getImage() == $_image->getData('file') ) { // Is base image if exsist go on top of array
	          		array_unshift($images, array(
		            	'id'=> $_image->getId(),
		            	'product_id'=> $simpleProduct->getId(),
		            	'product'=> $simpleProduct,
		            	'label'=> $_image->getLabel(),
		            	'image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($newWidth, $newHeight)),
		            	'thumb'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize(56))
	            	));
	          	} else {
	          		array_push($images, array(
		            	'id'=> $_image->getId(),
		            	'product_id'=> $simpleProduct->getId(),
		            	'product'=> $simpleProduct,
		            	'label'=> $_image->getLabel(),
		            	'image'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize($newWidth, $newHeight)),
		            	'thumb'=> sprintf(Mage::helper('catalog/image')->init($simpleProduct, 'image', $_image->getFile())->resize(56))
	            	));
	          	}
	          	
	          }
	        }
				}
			
			}
		}
		
		if (count($images) == 0) {
	
			
			foreach ($_product->getMediaGalleryImages() as $_image) {

				$images[] = array(
					'image'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize($imgElementWidth, $imgElementHeight)),
					'thumb'=> sprintf(Mage::helper('catalog/image')->init($_product, 'thumbnail', $_image->getFile())->resize(56)),
					'label'=> $_image->getLabel(),
					'id'=> $_image->getId(),
					'product_id'=> $productId,
					'product'=> $_product
				);

			}
		}

		if (count($images) == 0) {
		  echo "SMDesignColorswatchPreloader.removePerload($$('.product-image img')[0]);\n";
		  if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) {
		    echo "$$('.more-views ul')[0].update('');";
		  }
		  $image = Mage::helper('catalog/image')->init($_product, 'image')->resize(265);
		  echo "$$('.product-image img')[0].src = '{$image}?rand=' + Math.random();";
		  
		  exit();
		}

?>
$$('<?php echo $imageSelector ?>')[0].src = '<?php echo $images[0]['image']?>?rand=' + Math.random();

<?php if (Mage::getStoreConfig('smdesign_colorswatch/general/update_more_view')) : ?>
galleryView = $$('.more-views ul');

if (galleryView.length == 0) {
	galleryViewContainer = document.createElement('div');
	galleryViewContainer.className = 'more-views';
	galleryView = document.createElement('ul');
	galleryViewContainer.appendChild(galleryView);
	$$('.product-img-box')[0].appendChild(galleryViewContainer);
} else {
	galleryView = galleryView[0];
}

galleryView.update('');

<?php foreach ($images as $key=>$image) : ?>
	li = document.createElement('LI');
	<?php if (Mage::getStoreConfig('smdesign_colorswatch/zoom/enabled') && Mage::getStoreConfig('smdesign_colorswatch/zoom/more_view_change_main_image') && $_product->getData('enable_zoom_plugin') == 1 && ( 1 || $_product->getImage() != 'no_selection' && $_product->getImage() ) ) : ?>
		$(li).update("<a href=\"#\" onclick=\"SMDesignColorswatchPreloader.showPerload($('image')); $('image').src = '<?php echo $image['image']; ?>?rand=' + Math.random(); return false;\"><img src=\"<?php echo  $image['thumb'] ?>\" width=\"56\" height=\"56\" alt=\"<?php echo  $image['label'] ?>\" /></a>");
	<?php else : ?>
		$(li).update("<a href=\"#\" onclick=\"popWin('<?php echo Mage::getUrl('catalog/product/gallery', array('id'=>$image['product_id'], 'image'=>$image['id'], 'pid'=>$productId)) ?>', 'gallery', 'width=300,height=300,left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes'); return false;\" title=\"<?php echo  $image['label'] ?>\"><img src=\"<?php echo  $image['thumb'] ?>\" width=\"56\" height=\"56\" alt=\"<?php echo  $image['label'] ?>\" /></a>");
		
	<?php endif; ?>
	galleryView.appendChild(li);
<?php endforeach; ?>

<?php endif; ?>

<?php if (!(Mage::getStoreConfig('smdesign_colorswatch/zoom/enabled') && Mage::getStoreConfig('smdesign_colorswatch/zoom/more_view_change_main_image') && $_product->getData('enable_zoom_plugin') == 1 && ( $_product->getImage() != 'no_selection' && $_product->getImage() ) ) ): ?>
SMDesignColorswatchPreloader.removePerload($('image'));
<?php endif; ?>


<?php	}

	private function productExsist($products, $aCode, $oId) {
		foreach ($products as $key=>$product) {
			if ($product->getData($aCode) == $oId) {
				return true;
			}
		}
		return false;
		
	}
	
}