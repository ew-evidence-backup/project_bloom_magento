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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

require_once 'Mage/Downloadable/controllers/Adminhtml/Downloadable/FileController.php';

/**
 * Downloadable File upload controller
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 * @deprecated  after 1.4.2.0 Mage_Downloadable_Adminhtml_Downloadable_FileController is used
 */
class Mage_Downloadable_FileController extends Mage_Downloadable_Adminhtml_Downloadable_FileController
{
    /**
     * Controller predispatch method
     * Show 404 front page
     */
    public function preDispatch()
    {
        $this->_forward('defaultIndex', 'cms_index');
    }
}
