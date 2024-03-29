<?php
/**
 * Magento Commercial Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Commercial Edition License
 * that is available at: http://www.magentocommerce.com/license/commercial-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Gift Message url helper
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Helper_Url extends Mage_Core_Helper_Url
{
    /**
     * Retrive gift message save url
     *
     * @param Varien_Object $item
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getEditUrl(Varien_Object $item, $type, $params=array())
    {
         if($item->getGiftMessageId()) {
             $params = array_merge($params, array('message'=>$item->getGiftMessageId(), 'item'=>$item->getId(), 'type'=>$type));
             return $this->_getUrl('giftmessage/index/edit', $params);
         } else {
             $params = array_merge($params, array('item'=>$item->getId(), 'type'=>$type));
             return $this->_getUrl('giftmessage/index/new', $params);
         }
    }

    /**
     * Retrive gift message button block url
     *
     * @param integer $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getButtonUrl($itemId, $type, $params=array())
    {
         $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
         return $this->_getUrl('giftmessage/index/button', $params);
    }

    /**
     * Retrive gift message remove url
     *
     * @param integer $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getRemoveUrl($itemId, $type, $params=array())
    {
         $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
         return $this->_getUrl('giftmessage/index/remove', $params);
    }

    /**
     * Retrive gift message save url
     *
     * @param integer $itemId
     * @param string $type
     * @param string $giftMessageId
     * @param array $params
     * @return string
     */
    public function getSaveUrl($itemId, $type, $giftMessageId=null, $params=array())
    {
         if(!is_null($giftMessageId)) {
             $params = array_merge($params, array('message'=>$giftMessageId, 'item'=>$itemId, 'type'=>$type));
             return $this->_getUrl('giftmessage/index/save', $params);
         } else {
             $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
             return $this->_getUrl('giftmessage/index/save', $params);
         }
    }

}
