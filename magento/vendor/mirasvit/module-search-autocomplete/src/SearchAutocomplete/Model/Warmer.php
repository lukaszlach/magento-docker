<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-autocomplete
 * @version   1.0.36
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchAutocomplete\Model;

use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class Warmer
{
    /**
     * @var QueryCollectionFactory
     */
    protected $queryCollectionFactory;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @param QueryCollectionFactory $queryCollectionFactory
     * @param CacheInterface         $cacheInterface
     * @param ObjectManagerInterface $objectManager
     * @param QueryFactory           $queryFactory
     * @param StoreManagerInterface  $storeManager
     * @param ResourceConnection     $resource
     */
    public function __construct(
        QueryCollectionFactory $queryCollectionFactory,
        CacheInterface $cacheInterface,
        ObjectManagerInterface $objectManager,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        $this->queryCollectionFactory = $queryCollectionFactory;
        $this->cache = $cacheInterface;
        $this->objectManager = $objectManager;
        $this->queryFactory = $queryFactory;
        $this->resource = $resource;
        $this->connection = $this->resource->getConnection();
        $this->storeManager = $storeManager;
    }

    /**
     * Warm cache
     * @return void
     */
    public function warm()
    {
        $collection = $this->queryCollectionFactory->create()
            ->setOrder('popularity');

        /** @var \Magento\Search\Model\Query $query */
        foreach ($collection as $query) {
            $queryText = $query->getQueryText();

            $this->processQuery($queryText);
        }

        $indexTable = $this->resource->getTableName('mst_misspell_index');

        $select = $this->connection->select()->from($indexTable, ['keyword']);
        foreach ($this->connection->fetchCol($select) as $queryText) {
            $this->processQuery($queryText);
        }
    }

    protected function processQuery($query)
    {
        $store = $this->storeManager->getDefaultStoreView();

        $part = '';
        for ($i = 0; $i < strlen($query); $i++) {
            $part .= $query[$i];

            $identifier = 'QUERY_' . $store->getId() . '_' . md5($part);

            if (!$this->cache->load($identifier)) {
                $ts = microtime(true);

                $url = $store->getUrl('searchautocomplete/ajax/suggest', ['_query' => ['q' => $part]]);

                try {
                    file_get_contents($url);
                } catch (\Exception $e) {
                }

                $time = round(microtime(true) - $ts, 2);

                echo "'$part' has cached ($time)" . PHP_EOL;
            } else {
                echo "'$part' in cache" . PHP_EOL;
            }
        }
    }
}