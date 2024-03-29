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
 * Webservice soap adapter
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_WSI_Adapter_Soap extends Mage_Api_Model_Server_Adapter_Soap
{
    /**
     * Run webservice
     *
     * @param Mage_Api_Controller_Action $controller
     * @return Mage_Api_Model_Server_Adapter_Soap
     */
    public function run()
    {
        $apiConfigCharset = Mage::getStoreConfig("api/config/charset");

        if ($this->getController()->getRequest()->getParam('wsdl') !== null) {
            $wsdlConfig = Mage::getModel('api/wsdl_config');
            $wsdlConfig->setHandler($this->getHandler())
                ->init();
            $this->getController()->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type','text/xml; charset='.$apiConfigCharset)
                ->setBody(
                        preg_replace(
                            '/(\>\<)/i',
                            ">\n<",
                            str_replace(
                                    '<soap:operation soapAction=""></soap:operation>',
                                    "<soap:operation soapAction=\"\" />\n",
                                    str_replace(
                                            '<soap:body use="literal"></soap:body>',
                                            "<soap:body use=\"literal\" />\n",
                                            preg_replace(
                                                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                                                '<?xml version="$1" encoding="'.$apiConfigCharset.'"?>',
                                                $wsdlConfig->getWsdlContent()
                                            )
                                    )
                            )
                        )
                );
        } else {
            try {
                $this->_instantiateServer();

                $this->getController()->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type','text/xml; charset='.$apiConfigCharset)
                    ->setBody(
                        preg_replace(
                            '/(\>\<)/i',
                            ">\n<",
                            str_replace(
                                    '<soap:operation soapAction=""></soap:operation>',
                                    "<soap:operation soapAction=\"\" />\n",
                                    str_replace(
                                            '<soap:body use="literal"></soap:body>',
                                            "<soap:body use=\"literal\" />\n",
                                            preg_replace(
                                                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                                                '<?xml version="$1" encoding="'.$apiConfigCharset.'"?>',
                                                $this->_soap->handle()
                                            )
                                    )
                            )
                        )
                    );
            } catch( Zend_Soap_Server_Exception $e ) {
                $this->fault( $e->getCode(), $e->getMessage() );
            } catch( Exception $e ) {
                $this->fault( $e->getCode(), $e->getMessage() );
            }
        }

        return $this;
    }
}
