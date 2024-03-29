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
 * Tab design Accordion Images xml renderer
 *
 * @category     Mage
 * @package      Mage_Xmlconnect
 * @author       Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Images
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
{
    /**
     * Getter for accordion item title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__('Images');
    }

    /**
     * Getter for accordion item is open flag
     *
     * @return bool
     */
    public function getIsOpen()
    {
        return true;
    }

    /**
     * Prepare form
     *
     * @throws Mage_Core_Exception
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('field_logo', array());
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset,
            'conf[native][navigationBar][icon]',
            $this->__('Logo in Header'),
            $this->__('Recommended size 35px x 35px.'),
            $this->_getDesignPreviewImageUrl('conf/native/navigationBar/icon'),
            true
        );

        $deviceType = Mage::helper('xmlconnect')->getDeviceType();
        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $this->addImage($fieldset,
                    'conf[native][body][bannerImage]',
                    $this->__('Banner on Home Screen'),
                    $this->__('Recommended size 320px x 230px. Note: Image size affects the performance of your app. Keep your image size below 50 KB for optimal performance.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][backgroundImage]',
                    $this->__('App Background'),
                    $this->__('Recommended size 320px x 367px. Note: Image size affects the performance of your app. Keep your image size below 75 KB for optimal performance.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/backgroundImage'),
                    true
                );
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                $this->addImage($fieldset,
                    'conf[native][body][bannerIpadLandscapeImage]',
                    $this->__('Banner on Home Screen <br />(landscape mode)'),
                    $this->__('Recommended size 1024px x 344px. Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerIpadLandscapeImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][bannerIpadImage]',
                    $this->__('Banner on Home Screen <br />(portrait mode)'),
                    $this->__('Recommended size 768px x 294px. Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerIpadImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][backgroundIpadLandscapeImage]',
                    $this->__('App Background <br />(landscape mode)'),
                    $this->__('Recommended size 1024px x 704px. Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/backgroundIpadLandscapeImage'),
                    true
                );
                $this->addImage($fieldset,
                    'conf[native][body][backgroundIpadPortraitImage]',
                    $this->__('App Background <br />(portrait mode)'),
                    $this->__('Recommended size 768px x 960px. Note: Image size affects the performance of your app.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/backgroundIpadPortraitImage'),
                    true
                );
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                $this->addImage($fieldset,
                    'conf[native][body][bannerAndroidImage]',
                    $this->__('Banner on Home Screen'),
                    $this->__('Recommended size 320px x 258px. Note: Image size affects the performance of your app. Keep your image size below 50 KB for optimal performance.'),
                    $this->_getDesignPreviewImageUrl('conf/native/body/bannerAndroidImage'),
                    true
                );
                break;
            default:
                Mage::throwException(
                    $this->__('Device doesn\'t recognized: "%s". Unable to load a helper.', $deviceType)
                );
                break;
        }

        $form->setValues(Mage::helper('xmlconnect')->getApplication()->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

   /**
    * Retrieve url for images in the skin folder
    *
    * @param string $name - path to file name relative to the skin dir
    * @return string
    */
    protected function _getDesignPreviewImageUrl($name)
    {
        $name = Mage::helper('xmlconnect/image')->getInterfaceImagesPaths($name);
        return Mage::helper('xmlconnect/image')->getDefaultDesignUrl($name);
    }
}
