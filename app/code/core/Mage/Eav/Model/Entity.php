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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * EAV entity model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity extends Mage_Eav_Model_Entity_Abstract
{
    const DEFAULT_ENTITY_MODEL      = 'eav/entity';
    const DEFAULT_ATTRIBUTE_MODEL   = 'eav/entity_attribute';
    const DEFAULT_BACKEND_MODEL     = 'eav/entity_attribute_backend_default';
    const DEFAULT_FRONTEND_MODEL    = 'eav/entity_attribute_frontend_default';
    const DEFAULT_SOURCE_MODEL      = 'eav/entity_attribute_source_config';

    const DEFAULT_ENTITY_TABLE      = 'eav/entity';
    const DEFAULT_ENTITY_ID_FIELD   = 'entity_id';
    const DEFAULT_VALUE_TABLE_PREFIX= 'eav/entity_attribute';

    /**
     * Resource initialization
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setConnection($resource->getConnection('eav_read'));
    }

}
