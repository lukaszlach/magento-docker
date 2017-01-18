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

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SearchAutocomplete\Helper\Data as DataHelper;

/**
 * Class Result
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Result
{
    /**
     * @var LayerResolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Search\Model\Query
     */
    protected $query;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SearchHelper
     */
    protected $searchHelper;

    /**
     * @var Index\Pool
     */
    protected $indexPool;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var QueryCollectionFactory
     */
    protected $queryCollectionFactory;

    /**
     * @var bool
     */
    protected static $isLayerCreated = false;

    /**
     * @param LayerResolver          $layerResolver
     * @param QueryFactory           $queryFactory
     * @param Config                 $config
     * @param SearchHelper           $searchHelper
     * @param Index\Pool             $indexPool
     * @param DataHelper             $dataHelper
     * @param QueryCollectionFactory $queryCollectionFactory
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        LayerResolver $layerResolver,
        QueryFactory $queryFactory,
        Config $config,
        SearchHelper $searchHelper,
        Index\Pool $indexPool,
        DataHelper $dataHelper,
        QueryCollectionFactory $queryCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->layerResolver = $layerResolver;
        $this->query = $queryFactory->get();
        $this->config = $config;
        $this->searchHelper = $searchHelper;
        $this->indexPool = $indexPool;
        $this->dataHelper = $dataHelper;
        $this->queryCollectionFactory = $queryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * List of indexes
     *
     * @return array
     */
    public function getIndexes()
    {
        $indexes = $this->dataHelper->getEnabledIndexes();

        foreach ($indexes as $index) {
            $indexClass = '\Mirasvit\Search\Model\Index';
            if ($index instanceof $indexClass) {
                $index->setData('search_collection', $index->getSearchCollection());
            } else {
                /** @var \Magento\Framework\DataObject $index */
                if ($index->getData('code') == 'catalogsearch_fulltext') {
                    $index->setData(
                        'search_collection',
                        $this->layerResolver->get()->getProductCollection()
                    );
                } elseif ($index->getData('code') == 'magento_search_query') {
                    $index->setData(
                        'search_collection',
                        $this->queryCollectionFactory->create()
                            ->setQueryFilter($this->query->getQueryText())
                            ->addFieldToFilter('query_text', ['neq' => $this->query->getQueryText()])
                            ->addStoreFilter([$this->storeManager->getStore()->getId()])
                            ->setOrder('popularity')
                            ->distinct(true)
                    );
                }
            }
        }

        return $indexes;
    }

    /**
     * Search collection for index
     *
     * @param \Mirasvit\Search\Model\Index $index
     * @return array
     */
    public function getCollection($index)
    {
        $index->getSearchCollection()
            ->setPageSize((int)$this->config->getIndexOptionValue($index->getData('code'), 'limit'));

        return $index->getSearchCollection();
    }

    /**
     * @return void
     */
    public function init()
    {
        if (!self::$isLayerCreated) {
            try {
                $this->layerResolver->create(LayerResolver::CATALOG_LAYER_SEARCH);
            } catch (\Exception $e) {
            } finally {
                self::$isLayerCreated = true;
            }
        }
    }

    /**
     * Convert all results to array
     *
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function toArray()
    {
        $result = [
            'totalItems' => 0,
            'query'      => $this->query->getQueryText(),
            'indexes'    => [],
            'noResults'  => false,
            'urlAll'     => $this->searchHelper->getResultUrl($this->query->getQueryText()),
        ];

        /** @var \Mirasvit\Search\Model\Index $index */
        foreach ($this->getIndexes() as $index) {
            $indexCode = $index->getCode();

            if ($indexCode == 'catalogsearch_fulltext') {
                $indexCode = 'magento_catalog_product';
            }

            $collection = $this->getCollection($index);

            $localIndex = $this->indexPool->get($indexCode);

            $localIndex->setCollection($collection);

            $items = [
                'code'         => $indexCode,
                'title'        => $index->getTitle(),
                'totalItems'   => $collection->getSize(),
                'isShowTotals' => $indexCode == 'magento_search_query' ? false : true,
                'items'        => $localIndex->getItems(),
            ];

            $result['indexes'][] = $items;

            $result['totalItems'] += $localIndex->getSize();
        }

        $result['textAll'] = __('Show all %1 results →', $result['totalItems']);
        $result['textEmpty'] = __('Sorry, nothing found for "%1".', $result['query']);

        $result['noResults'] = $result['totalItems'] ? false : true;

        return $result;
    }
}
