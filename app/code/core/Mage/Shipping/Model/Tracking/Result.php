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
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

class Mage_Shipping_Model_Tracking_Result
{

    protected $_trackings = array();
    protected $_error = null;

    /**
     * Reset tracking
     */
    public function reset()
    {
        $this->_trackings = array();
        return $this;
    }

    public function setError($error)
    {
        $this->_error = $error;
    }

    public function getError()
    {
        return $this->_error;
    }
    /**
     * Add a tracking to the result
     */
    public function append($result)
    {
        if ($result instanceof Mage_Shipping_Model_Tracking_Result_Abstract) {
            $this->_trackings[] = $result;
        } elseif ($result instanceof Mage_Shipping_Model_Rate_Result) {
            $trackings = $result->getAllTrackings();
            foreach ($trackings as $track) {
                $this->append($track);
            }
        }
        return $this;
    }

    /**
     * Return all trackings in the result
     */
    public function getAllTrackings()
    {
        return $this->_trackings;
    }

}
