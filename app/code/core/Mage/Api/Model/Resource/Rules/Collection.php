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
 * @package     Mage_Api
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */


/**
 * Api Rules Resource Collection
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Resource_Rules_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api/rules');
    }

    /**
     * Retrieve rules by role
     *
     * @param int $id
     * @return Mage_Api_Model_Resource_Rules_Collection
     */
    public function getByRoles($id)
    {
        $this->getSelect()->where("role_id = ?", (int)$id);
        return $this;
    }

    /**
     * Add sort by length
     *
     * @return Mage_Api_Model_Resource_Rules_Collection
     */
    public function addSortByLength()
    {
        $this->getSelect()->columns(array('length' => $this->getConnection()->getLengthSql('resource_id')))
            ->order('length DESC');
        return $this;
    }
}
