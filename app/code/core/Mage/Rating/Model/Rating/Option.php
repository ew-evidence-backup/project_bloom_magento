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
 * @package     Mage_Rating
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Rating option model
 *
 * @method Mage_Rating_Model_Resource_Rating_Option _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option getResource()
 * @method int getRatingId()
 * @method Mage_Rating_Model_Rating_Option setRatingId(int $value)
 * @method string getCode()
 * @method Mage_Rating_Model_Rating_Option setCode(string $value)
 * @method int getValue()
 * @method Mage_Rating_Model_Rating_Option setValue(int $value)
 * @method int getPosition()
 * @method Mage_Rating_Model_Rating_Option setPosition(int $value)
 *
 * @category    Mage
 * @package     Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Rating_Option extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rating/rating_option');
    }

    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }

//    public function getId()
//    {
//        return $this->getOptionId();
//    }
}
