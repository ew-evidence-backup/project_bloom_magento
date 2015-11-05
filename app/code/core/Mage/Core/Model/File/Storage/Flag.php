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
 * Synchronize process status flag class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Storage_Flag extends Mage_Core_Model_Flag
{
    /**
     * There was no synchronization
     */
    const STATE_INACTIVE    = 0;
    /**
     * Synchronize process is active
     */
    const STATE_RUNNING     = 1;
    /**
     * Synchronization finished
     */
    const STATE_FINISHED    = 2;
    /**
     * Synchronization finished and notify message was formed
     */
    const STATE_NOTIFIED    = 3;

    /**
     * Flag time to life in seconds
     */
    const FLAG_TTL          = 300;

    /**
     * Synchronize flag code
     *
     * @var string
     */
    protected $_flagCode    = 'synchronize';

    /**
     * Pass error to flag
     *
     * @param Exception $e
     * @return Mage_Core_Model_File_Storage_Flag
     */
    public function passError(Exception $e)
    {
        $data = $this->getFlagData();
        if (!is_array($data)) {
            $data = array();
        }
        $data['has_errors'] = true;
        $this->setFlagData($data);
        return $this;
    }
}
