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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Adminhtml system template grid type filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Email_Template_Grid_Filter_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected static $_types = array(
        null										=>	null,
        Mage_Newsletter_Model_Template::TYPE_HTML   => 'HTML',
        Mage_Newsletter_Model_Template::TYPE_TEXT 	=> 'Text',
    );

    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_types as $code=>$label) {
            $result[] = array('value'=>$code, 'label'=>Mage::helper('adminhtml')->__($label));
        }

        return $result;
    }


    public function getCondition()
    {
        if(is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }


}// Class Mage_Adminhtml_Block_Newsletter_Queue_Grid_Filter_Status END
