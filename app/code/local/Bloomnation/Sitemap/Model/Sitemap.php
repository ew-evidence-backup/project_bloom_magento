<?php
class Bloomnation_Sitemap_Model_Sitemap extends Mage_Sitemap_Model_Sitemap
{
    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        if ($io->fileExists($this->getSitemapFilename()) && !$io->isWriteable($this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getSitemapFilename());

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:content="http://www.google.com/schemas/sitemap-content/1.0" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n");

        $storeId = $this->getStoreId();
        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        // Hans2103 change -> set mediaUrl
        $mediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $mediaUrl = preg_replace('/^https/', 'http', $mediaUrl); 

        /**
         * Generate categories sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>' . "\n",
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
            $this->check_counter($io);
        }
        unset($collection);

        /**
         * Generate products sitemap
         */
        /**
         * override to include images in sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/product/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf('<url><loc>%s</loc><image:image><image:loc>%s</image:loc><image:title>%s</image:title></image:image><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority><PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0"><DataObject type="thumbnail"><Attribute name="name" value="%s"/><Attribute name="src" value="%s"/></DataObject></PageMap></url>' . "\n",
                htmlspecialchars($baseUrl . $item->getUrl()),
                htmlspecialchars($mediaUrl .'catalog/product'. $item->getMedia()),
                htmlspecialchars($item->getName()),
                $date,
                $changefreq,
                $priority,
                htmlspecialchars($item->getName()),
                htmlspecialchars($mediaUrl .'catalog/product'. $item->getMedia())
            );
            $io->streamWrite($xml);
        }
        unset($collection);

        /**
         * Generate cms pages sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/page/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/page/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>' . "\n",
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }
        unset($collection);

        $io->streamWrite('</urlset>');
        $io->streamClose();

        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }
    public function check_counter(&$io) {
        static $counter;
        $counter++;
        if (($counter%40000) == 0) {
            $io->streamWrite('</urlset>');
            $io->streamClose();
            $filename = preg_replace('/\.xml/', '-'.round($counter/40000).'.xml', $this->getSitemapFilename());
            $io->streamOpen($filename);
            $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
        }
    }
}
