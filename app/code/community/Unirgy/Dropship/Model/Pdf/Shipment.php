<?php

class Unirgy_Dropship_Model_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Shipment
{
    protected function _setFontRegular($object, $size = 7)
    {
        if (!$this->getUseFont()) {
            return parent::_setFontRegular($object, $size);
        }
        $font = Zend_Pdf_Font::fontWithName(constant('Zend_Pdf_Font::FONT_'.$this->getUseFont()));
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontBold($object, $size = 7)
    {
        if (!$this->getUseFont()) {
            return parent::_setFontBold($object, $size);
        }
        $font = Zend_Pdf_Font::fontWithName(constant('Zend_Pdf_Font::FONT_'.$this->getUseFont().'_BOLD'));
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontItalic($object, $size = 7)
    {
        if (!$this->getUseFont()) {
            return parent::_setFontItalic($object, $size);
        }
        $font = Zend_Pdf_Font::fontWithName(constant('Zend_Pdf_Font::FONT_'.$this->getUseFont().'_ITALIC'));
        $object->setFont($font, $size);
        return $font;
    }

    protected $_currentShipment;

    public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');
        $pdfHelper = Mage::helper('urls/pdf');
        $pdf = new Zend_Pdf();
        if (method_exists($this, '_setPdf')) $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
            }

            $this->_currentShipment = $shipment;

            $order = $shipment->getOrder();
            
            // Add body
            foreach ($shipment->getAllItems() as $item){
                
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

                // Add image
                $this->_insertLogo($page, $shipment->getStore());
                // Insert order number
                $this->_setFontBold($page, 16);
                $page->drawText('Order number #'.$order->getIncrementId(), 200, 712, 'UTF-8');
    
                $firstColumnX = 254;
                $secondColumnX = 434;
                // Delivery Date
                $this->_setFontBold($page,16);
                $page->drawText('Delivery Date', $firstColumnX, 630, 'UTF-8');
                $this->_setFontRegular($page,14);
                $options = $item->getOrderItem()->getProductOptions();
                preg_match('/(\d{4})-(\d\d)-(\d\d)/', $item->getOrderItem()->getDeliveryDate() ,$delDate);
                $delivDate = $delDate[2].'/'.$delDate[3].'/'.$delDate[1];                
                $page->drawText($delivDate, $firstColumnX, 610, 'UTF-8');

                // Delivery Address
                $this->_setFontBold($page,17);
                $page->drawText('Delivery Address', $firstColumnX, 560, 'UTF-8');                
                $shippingAddress = $this->_formatAddress(
                    Mage::helper('udropship')->formatCustomerAddress($order->getShippingAddress(), 'pdf', $this->_currentShipment->getUdropshipVendor())
                );
                $yCounter = 540;
                $this->_setFontRegular($page,14);
                foreach($shippingAddress as $adr) {
                    $page->drawText($adr, $firstColumnX, $yCounter, 'UTF-8');
                    $yCounter-= 15;
                }

                // Card Message
                if($item->getOrderItem()->getGiftMessageId()) {
                    $giftId = $item->getOrderItem()->getGiftMessageId();
                } elseif($order->getGiftMessageId()) {
                    $giftId = $order->getGiftMessageId();
                } else {
                    $giftId = false;
                }
                if($giftId) {
                    $this->_setFontBold($page,16);
                    $page->drawText('Card Message', $firstColumnX, 430, 'UTF-8');                      
                    // Add message text
                    $this->_setFontRegular($page,14);
                    $message = Mage::getModel('giftmessage/message')->load($giftId)->getMessage();

                    $cardMsgArr = $this->_formatMessageForPdf($message, 7);
                    $yCounter = 410;
                    foreach($cardMsgArr as $msgPart) {
                        $page->drawText($msgPart, $firstColumnX, $yCounter, 'UTF-8');
                        $yCounter -= 15;
                    }
                }
          
                $yCounter -=20;
                $this->_setFontBold($page,16);
                $iconArray['spec'] = $yCounter-10;
                $page->drawText('Special Instructions', $firstColumnX, $yCounter, 'UTF-8');
                // Special instructions
                if('' != $order->getCustomerNote()) {
                    // Add message text
                    $this->_setFontRegular($page,14);
                    $message = trim($order->getCustomerNote());
                    $cardMsgArr = $this->_formatMessageForPdf($message, 7);
                    $yCounter = 300;
                    foreach($cardMsgArr as $msgPart) {
                        $page->drawText($msgPart, $firstColumnX, $yCounter, 'UTF-8');
                        $yCounter -= 15;
                    }
                }

                // Sender and Recipient 
                $billingAddress = $this->_formatAddress(
                    Mage::helper('udropship')->formatCustomerAddress($order->getBillingAddress(), 'pdf', $this->_currentShipment->getUdropshipVendor())
                );
                $yCounter -=30;
                $iconArray['rec'] = $yCounter-10;
                $this->_setFontBold($page,16);
                $page->drawText('Recipient', $firstColumnX, $yCounter, 'UTF-8');
                $page->drawText('Sender', $secondColumnX, $yCounter, 'UTF-8');
                $this->_setFontRegular($page,14);
                $page->drawText($shippingAddress[0], $firstColumnX, $yCounter-20, 'UTF-8');
                $page->drawText($billingAddress[0], $secondColumnX, $yCounter-20, 'UTF-8');
                $page->drawText($order->getShippingAddress()->getTelephone(), $firstColumnX, $yCounter-40, 'UTF-8');
                $page->drawText($order->getBillingAddress()->getTelephone(), $secondColumnX, $yCounter-40, 'UTF-8');
                
                // Product image, name, price, description
                //image
                $productModel = Mage::getModel('catalog/product')->load($item->getProductId());
                $img = $productModel->getImageUrl();
                if('' != $img) {
                    $img = Mage::getBaseDir().'/'.str_replace(Mage::getUrl(''),'',$img);
                    try {
                        $image = Zend_Pdf_Image::imageWithPath($img);
                    } catch (Exception $e) {
                        $page->drawText($e->getMessage(), 25, $yCounter, 'UTF-8');
                        $pdf->pages[] = $page;
                        return $pdf;
                    }
                    $boundX = 100;
                    $boundY = 150;
                    $imgSize = getimagesize($img);
                    if(0 != $imgSize[0]) {
                        $xRatio = round($imgSize[0] / $boundX);
                        $yRatio = round($imgSize[1] / $boundY);
                        if($xRatio > $yRatio) {
                            $imgResize[0] = round($imgSize[0] / $xRatio);
                            $imgResize[1] = round($imgSize[1] / $xRatio);
                        } else {
                            $imgResize[0] = round($imgSize[0] / $yRatio);
                            $imgResize[1] = round($imgSize[1] / $yRatio);                            
                        }
                    }
                    $page->drawImage($image, 25, 500, 25+$imgResize[0], 500+$imgResize[1]);
                }
                $this->_setFontBold($page,16);
                //name
                    $nameMsgArr = $this->_formatMessageForPdf($item->getName(), 3);
                    $yCounter = 485;
                    foreach($nameMsgArr as $msgPart) {
                        $page->drawText($msgPart, 25, $yCounter, 'UTF-8');
                        $yCounter -= 17;
                    }
                    
                //price
                $yCounter -= 3; // -17 from foreach cycle befor and -3 here = -20 total
                $page->drawText(strip_tags(Mage::helper('checkout')->formatPrice($item->getPrice())), 25, $yCounter, 'UTF-8');
                // delivery charge
                $this->_setFontBold($page,12);
                $yCounter-= 20;
                $page->drawText('Total Delivery Charge: '.
                                strip_tags(Mage::helper('checkout')
                                           ->formatPrice(Mage::helper('udropship')
                                                ->getVendor($this->_currentShipment->getUdropshipVendor())
                                                    ->getDeliveryCharge())), 25, $yCounter, 'UTF-8');
                // description
                $this->_setFontRegular($page,14);
                $yCounter -= 20;
                $txtArr = $this->_formatMessageForPdf($productModel->getShortDescription(), 3);
                foreach($txtArr as $msgPart) {
                    $page->drawText($msgPart, 25, $yCounter, 'UTF-8');
                    $yCounter -= 15;
                }                
                
                // Separator line
                $page->drawLine(210, 180, 210, 650);
                // Add all icons
                $this->_insertIcons($page,$iconArray);

                $pdf->pages[] = $page;
            }            
            


            /* Add address 
            //$this->insertAddress($page, $shipment->getStore());

            /* Add head 
            $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId()));

            /*
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            $page->drawText(Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId(), 35, 780, 'UTF-8');

            /* Add table 
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            */

            /* Add table head 
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Qty'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Products'), 60, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 470, $this->y, 'UTF-8');

            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            */

            
        }

        $this->_afterGetPdf();

        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    protected function _formatMessageForPdf($text, $wordsPerLine) {
        $text = preg_replace('/\n/',' ',$text);
        
        $textArr = explode(' ', $text);
        $chunkArr = array_chunk($textArr, $wordsPerLine);
        foreach($chunkArr as $i=>$el) {
            $chunkArr[$i] = implode(' ',$el);
        }
        return $chunkArr;
    }

    protected function _insertIcons(&$page,$yPos = array()) {
        $base = Mage::getBaseDir();
        $images = array(
            array('image'=>$base.'/ghassets/pdfgen/calendar_sticker.jpeg'       ,'x1'=>'215','y1'=>'616','x2'=>'247','y2'=>'648'),
            array('image'=>$base.'/ghassets/pdfgen/home_sticker.jpeg'           ,'x1'=>'215','y1'=>'545','x2'=>'247','y2'=>'577'),
            array('image'=>$base.'/ghassets/pdfgen/mail-open_sticker.jpeg'      ,'x1'=>'215','y1'=>'414','x2'=>'247','y2'=>'446'),
            array('image'=>$base.'/ghassets/pdfgen/document-edit_sticker.jpeg'  ,'x1'=>'215','y1'=>$yPos['spec'],'x2'=>'247','y2'=>$yPos['spec']+32),
            array('image'=>$base.'/ghassets/pdfgen/address-book_sticker.jpeg'   ,'x1'=>'215','y1'=>$yPos['rec'],'x2'=>'247','y2'=>$yPos['rec']+32),
        );
        
        $this->_drawImageArray($page,$images);
    }

    protected function _insertLogo(&$page) {
        $image = Mage::getBaseDir() . '/ghassets/pdfgen/logo.png';
        if (is_file($image)) {
            $image = Zend_Pdf_Image::imageWithPath($image);
            $page->drawImage($image, 45, 730, 545, 825);
        }
    }

    protected function _drawImageArray(&$page, array $images) {
        foreach($images as $img) {
            $image = Zend_Pdf_Image::imageWithPath($img['image']);
            $page->drawImage($image,$img['x1'],$img['y1'],$img['x2'],$img['y2']);
        }
    }

    protected function insertOrder(&$page, $order, $putOrderId = true)
    {
        /*
        /* @var $order Mage_Sales_Model_Order 
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

        $page->drawRectangle(25, 790, 570, 755);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);

        if (Mage::helper('udropship')->isUdpoActive()) {
            $po = Mage::helper('udpo')->getShipmentPo($this->_currentShipment);
        }

        if ($putOrderId) {
            $page->drawText(Mage::helper('sales')->__('Order # ').$order->getRealOrderId(), 35, 770, 'UTF-8');
            if (!empty($po)) {
                $page->drawText(Mage::helper('udpo')->__('Purchase Order # ').$po->getIncrementId(), 135, 770, 'UTF-8');
            }
        }
        //$page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 35, 760, 'UTF-8');
        if (!empty($po)) {
            $page->drawText(Mage::helper('udpo')->__('Purchase Order Date: ') . Mage::helper('core')->formatDate($po->getCreatedAtStoreDate(), 'medium', false), 135, 760, 'UTF-8');
        }

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 755, 275, 730);
        $page->drawRectangle(275, 755, 570, 730);

        /* Calculate blocks info 

        /* Billing Address 
        $billingAddress = $this->_formatAddress(
            Mage::helper('udropship')->formatCustomerAddress($order->getBillingAddress(), 'pdf', $this->_currentShipment->getUdropshipVendor())
        );

        /* Payment 
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value))==''){
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method 
        if (!$order->getIsVirtual()) {
            /* Shipping Address 
            $shippingAddress = $this->_formatAddress(
                Mage::helper('udropship')->formatCustomerAddress($order->getShippingAddress(), 'pdf', $this->_currentShipment->getUdropshipVendor())
            );

            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740 , 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740 , 'UTF-8');
        }
        else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740 , 'UTF-8');
        }

        if (!$order->getIsVirtual()) {
            $y = 730 - (max(count($billingAddress), count($shippingAddress)) * 10 + 5);
        }
        else {
            $y = 730 - (count($billingAddress) * 10 + 5);
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, 730, 570, $y);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $this->y = 720;

        foreach ($billingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 35, $this->y, 'UTF-8');
                $this->y -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y = 720;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
                    $this->y -=10;
                }

            }

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);

            $this->y -=15;
            $this->_setFontBold($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = 720;
            $paymentLeft = 285;
        }

        foreach ($payment as $value){
            if (trim($value)!=='') {
                $page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
                $yPayments -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y -=15;

            $page->drawText($shippingMethod, 285, $this->y, 'UTF-8');

            $yShipments = $this->y;

            $curVendor = Mage::helper('udropship')->getVendor($this->_currentShipment->getUdropshipVendor());
            if (!$curVendor->getHidePackingslipAmount()) {
                $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getShippingAmount()) . ")";

                $page->drawText($totalShippingChargesText, 285, $yShipments-7, 'UTF-8');
            }
            $yShipments -=10;
            $tracks = $order->getTracksCollection();
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(380, $yShipments, 380, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

                $yShipments -=17;
                $this->_setFontRegular($page, 6);
                foreach ($order->getTracksCollection() as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode!='custom')
                    {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $carrier->getConfigData('title');
                    }
                    else
                    {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $truncatedTitle = substr($track->getTitle(), 0, 45) . (strlen($track->getTitle()) > 45 ? '...' : '');
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 300, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 395, $yShipments , 'UTF-8');
                    $yShipments -=7;
                }
            } else {
                $yShipments -= 7;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $this->y + 15, 25, $currentY);
            $page->drawLine(25, $currentY, 570, $currentY);
            $page->drawLine(570, $currentY, 570, $this->y + 15);

            $this->y = $currentY;
            $this->y -= 15;
        }
        */
    }
}
