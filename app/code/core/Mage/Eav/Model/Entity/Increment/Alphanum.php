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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Enter description here...
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
 */
class Mage_Eav_Model_Entity_Increment_Alphanum extends Mage_Eav_Model_Entity_Increment_Abstract
{
    public function getAllowedChars()
    {
        return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    public function getNextId()
    {
        $lastId = $this->getLastId();

        if (strpos($lastId, $this->getPrefix())===0) {
            $lastId = substr($lastId, strlen($this->getPrefix()));
        }

        $lastId = str_pad((string)$lastId, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);

        $nextId = '';
        $bumpNextChar = true;
        $chars = $this->getAllowedChars();
        $lchars = strlen($chars);
        $lid = strlen($lastId)-1;

        for ($i = $lid; $i >= 0; $i--) {
            $p = strpos($chars, $lastId{$i});
            if (false===$p) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid character encountered in increment ID: %s', $lastId));
            }
            if ($bumpNextChar) {
                $p++;
                $bumpNextChar = false;
            }
            if ($p===$lchars) {
                $p = 0;
                $bumpNextChar = true;
            }
            $nextId = $chars{$p}.$nextId;
        }

        return $this->format($nextId);
    }
}
