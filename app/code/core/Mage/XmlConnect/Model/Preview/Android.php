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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/**
 * Android preview model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Preview_Android extends Mage_XmlConnect_Model_Preview_Abstract
{
    /**
     * Current device orientation
     *
     * @var string
     */
    protected $_orientation = 'unknown';

    /**
     * Set device orientation
     *
     * @param string $orientation
     * @return Mage_XmlConnect_Model_Preview_Android
     */
    public function setOrientation($orientation)
    {
        $this->_orientation = $orientation;
        return $this;
    }

    /**
     * Get current device orientation
     *
     * @return string
     */
    public function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * Get application banner image url
     *
     * @return string
     */
    public function getBannerImage()
    {
        $configPath = 'conf/body/bannerAndroidImage';
        if ($this->getData($configPath)) {
            $bannerImage = $this->getData($configPath);
        } else {
            $bannerImage = $this->getPreviewImagesUrl('android/bg_logo.png');
        }
        return $bannerImage;
    }

    /**
     * We doesn't support background images for android
     *
     * @return false
     */
    public function getBackgroundImage()
    {
        return false;
    }
}
