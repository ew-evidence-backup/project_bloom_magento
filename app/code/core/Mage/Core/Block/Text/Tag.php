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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Text_Tag extends Mage_Core_Block_Text
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTagParams(array());
    }

    function setTagParam($param, $value=null)
    {
        if (is_array($param) && is_null($value)) {
            foreach ($param as $k=>$v) {
                $this->setTagParam($k, $v);
            }
        } else {
            $params = $this->getTagParams();
            $params[$param] = $value;
            $this->setTagParams($params);
        }
        return $this;
    }

    function setContents($text)
    {
        $this->setTagContents($text);
        return $this;
    }

    protected function _toHtml()
    {
        $this->setText('<'.$this->getTagName().' ');
        if ($this->getTagParams()) {
            foreach ($this->getTagParams() as $k=>$v) {
                $this->addText($k.'="'.$v.'" ');
            }
        }

        $this->addText('>'.$this->getTagContents().'</'.$this->getTagName().'>'."\r\n");
        return parent::_toHtml();
    }

}
