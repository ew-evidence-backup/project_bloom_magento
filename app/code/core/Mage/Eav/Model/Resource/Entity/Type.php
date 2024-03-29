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
 * EAV entity type resource model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/entity_type', 'entity_type_id');
    }

    /**
     * Load Entity Type by Code
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $code
     * @return Mage_Eav_Model_Resource_Entity_Type
     */
    public function loadByCode($object, $code)
    {
        return $this->load($object, $code, 'entity_type_code');
    }

    /**
     * Retrieve additional attribute table name for specified entity type
     *
     * @param integer $entityTypeId
     * @return string
     */
    public function getAdditionalAttributeTable($entityTypeId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('entity_type_id' => $entityTypeId);
        $select  = $adapter->select()
            ->from($this->getMainTable(), array('additional_attribute_table'))
            ->where('entity_type_id = :entity_type_id');

        return $adapter->fetchOne($select, $bind);
    }
}
