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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Tag data helper
 */
class Mage_Tag_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStatusesArray()
    {
        return array(
            Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
            Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('tag')->__('Pending'),
            Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved')
        );
    }

    public function getStatusesOptionsArray()
    {
        return array(
            array(
                'label' => Mage::helper('tag')->__('Disabled'),
                'value' => Mage_Tag_Model_Tag::STATUS_DISABLED
            ),
            array(
                'label' => Mage::helper('tag')->__('Pending'),
                'value' => Mage_Tag_Model_Tag::STATUS_PENDING
            ),
            array(
                'label' => Mage::helper('tag')->__('Approved'),
                'value' => Mage_Tag_Model_Tag::STATUS_APPROVED
            )
        );
    }

    /**
     * Check tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    public function extractTags($tagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clear tag from the separating characters
     *
     * @param array $tagNamesArr
     * @return array
     */
    public function cleanTags(array $tagNamesArr)
    {
        foreach ($tagNamesArr as $key => $tagName) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if ($tagNamesArr[$key] == '') {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }

}
