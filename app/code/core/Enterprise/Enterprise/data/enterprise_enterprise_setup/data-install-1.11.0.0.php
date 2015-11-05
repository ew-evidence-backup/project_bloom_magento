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
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/commercial-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$tablePage = $installer->getTable('cms/page');

// add fancy 404 page content
$page = $installer->getConnection()->fetchRow($installer->getConnection()->select()
    ->from($tablePage, array('page_id', 'content'))
    ->where('identifier = ?', 'no-route')
    ->limit(1));
if ($page) {
    $content = '<div class="page-head-alt"><h3>We are sorry, but the page you are looking for cannot be found.</h3></div>
<div>
    <ul class="disc">
        <li>If you typed the URL directly, please make sure the spelling is correct.</li>
        <li>If you clicked on a link to get here, we must have moved the content.<br/>Please try our store search box above to search for an item.</li>
        <li>If you are not sure how you got here, <a href="#" onclick="history.go(-1);">go back</a> to the previous page</a> or return to our <a href="{{store url=""}}">store homepage</a>.</li>
    </ul>
</div>' . "\n\n<!-- " . $page['content'] . ' -->';

    $installer->getConnection()->update(
        $tablePage,
        array('content' => $content),
        array('page_id = ?' => $page['page_id'])
    );
}
