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
 * Rules resource model
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Resource_Rules extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api/rule', 'rule_id');
    }

    /**
     * Save rule
     *
     * @param Mage_Api_Model_Rules $rule
     */
    public function saveRel(Mage_Api_Model_Rules $rule)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $roleId = $rule->getRoleId();
            $adapter->delete($this->getMainTable(), array('role_id = ?' => $roleId));
            $masterResources = Mage::getModel('api/roles')->getResourcesList2D();
            $masterAdmin = false;
            if ($postedResources = $rule->getResources()) {
                foreach ($masterResources as $index => $resName) {
                    if (!$masterAdmin) {
                        $permission = (in_array($resName, $postedResources))? 'allow' : 'deny';
                        $adapter->insert($this->getMainTable(), array(
                            'role_type'     => 'G',
                            'resource_id'   => trim($resName, '/'),
                            'api_privileges'    => null,
                            'assert_id'     => 0,
                            'role_id'       => $roleId,
                            'api_permission'    => $permission
                            ));
                    }
                    if ($resName == 'all' && $permission == 'allow') {
                        $masterAdmin = true;
                    }
                }
            }

            $adapter->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $adapter->rollBack();
        }
    }
}
