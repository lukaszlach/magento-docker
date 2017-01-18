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
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.34
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchMysql\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder as AggregationBuilder;
use Magento\Framework\Search\Adapter\Mysql\Mapper;
use Magento\Framework\Search\Adapter\Mysql\ResponseFactory;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Mirasvit\Search\Helper\Data as SearchHelper;
use Mirasvit\Search\Model\Config as SearchConfig;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Adapter implements AdapterInterface
{
    /**
     * Mapper instance
     *
     * @var Mapper
     */
    protected $mapper;

    /**
     * Response Factory
     *
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var AggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * @var SearchHelper
     */
    protected $searchHelper;

    /**
     * @var SearchConfig
     */
    protected $searchConfig;

    /**
     * @param Mapper                  $mapper
     * @param ResponseFactory         $responseFactory
     * @param ResourceConnection      $resource
     * @param AggregationBuilder      $aggregationBuilder
     * @param TemporaryStorageFactory $temporaryStorageFactory
     * @param SearchHelper            $searchHelper
     * @param SearchConfig            $searchConfig
     */
    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        ResourceConnection $resource,
        AggregationBuilder $aggregationBuilder,
        TemporaryStorageFactory $temporaryStorageFactory,
        SearchHelper $searchHelper,
        SearchConfig $searchConfig
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->resource = $resource;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->searchHelper = $searchHelper;
        $this->searchConfig = $searchConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function query(RequestInterface $request)
    {
        $query = $this->mapper->buildQuery($request);

        if ($request->getName() == 'quick_search_container') {
            $query->limit($this->searchConfig->getResultsLimit());
        }

        if (isset($_GET) && isset($_GET['debug'])) {
            echo '<hr>'.$query.'<hr>';
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();

        $table = $temporaryStorage->storeDocumentsFromSelect($query);

        $this->searchHelper->prepareTemporaryTable($table);

        $documents = $this->getDocuments($table);

        $aggregations = $this->aggregationBuilder->build($request, $table);
        $response = [
            'documents'    => $documents,
            'aggregations' => $aggregations,
        ];

        return $this->responseFactory->create($response);
    }

    /**
     * Executes query and return raw response
     *
     * @param Table $table
     * @return array
     * @throws \Zend_Db_Exception
     */
    private function getDocuments(Table $table)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from($table->getName(), ['entity_id', 'score']);

        return $connection->fetchAssoc($select);
    }

    /**
     * @return false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }
}
