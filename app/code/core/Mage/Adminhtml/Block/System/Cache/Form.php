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
 * Cache management form page
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Cache_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize cache management form
     *
     * @return Mage_Adminhtml_Block_System_Cache_Form
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend' => Mage::helper('adminhtml')->__('Cache Control')
        ));

        $fieldset->addField('all_cache', 'select', array(
            'name'=>'all_cache',
            'label'=>'<strong>'.Mage::helper('adminhtml')->__('All Cache').'</strong>',
            'value'=>1,
            'options'=>array(
                '' => Mage::helper('adminhtml')->__('No change'),
                'refresh' => Mage::helper('adminhtml')->__('Refresh'),
                'disable' => Mage::helper('adminhtml')->__('Disable'),
                'enable' => Mage::helper('adminhtml')->__('Enable'),
            ),
        ));

        foreach (Mage::helper('core')->getCacheTypes() as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'checkbox', array(
                'name'=>'enable['.$type.']',
                'label'=>Mage::helper('adminhtml')->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
                //'options'=>$options,
            ));
        }

        $fieldset = $form->addFieldset('beta_cache_enable', array(
            'legend' => Mage::helper('adminhtml')->__('Cache Control (beta)')
        ));

        foreach (Mage::helper('core')->getCacheBetaTypes() as $type=>$label) {
            $fieldset->addField('beta_enable_'.$type, 'checkbox', array(
                'name'=>'beta['.$type.']',
                'label'=>Mage::helper('adminhtml')->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
            ));
        }

        $this->setForm($form);

        return $this;
    }
}
