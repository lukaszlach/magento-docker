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


namespace Mirasvit\SearchSphinx\Model;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder as MysqlAggregationBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\Adapter\Mysql\ResponseFactory as MysqlResponseFactory;
use Magento\Framework\Search\Adapter\Mysql\DocumentFactory as MysqlDocumentFactory;
use Magento\Framework\App\ObjectManager;
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
     * @var Adapter\Mapper
     */
    protected $mapper;

    /**
     * Response Factory
     *
     * @var MysqlResponseFactory
     */
    protected $responseFactory;

    /**
     * @var MysqlAggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var TemporaryStorageFactory
     */
    protected $temporaryStorageFactory;

    /**
     * @var SearchHelper
     */
    protected $searchHelper;

    /**
     * @var SearchConfig
     */
    protected $searchConfig;

    /**
     * @param Adapter\Mapper          $mapper
     * @param MysqlResponseFactory    $responseFactory
     * @param MysqlAggregationBuilder $aggregationBuilder
     * @param TemporaryStorageFactory $temporaryStorageFactory
     * @param SearchHelper            $searchHelper
     * @param SearchConfig            $searchConfig
     * @param MysqlDocumentFactory    $documentFactory
     * @param ResourceConnection      $resource
     */
    public function __construct(
        Adapter\Mapper $mapper,
        MysqlResponseFactory $responseFactory,
        MysqlAggregationBuilder $aggregationBuilder,
        TemporaryStorageFactory $temporaryStorageFactory,
        SearchHelper $searchHelper,
        SearchConfig $searchConfig,
        MysqlDocumentFactory $documentFactory,
        ResourceConnection $resource
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->searchHelper = $searchHelper;
        $this->searchConfig = $searchConfig;
        $this->documentFactory = $documentFactory;
        $this->resource = $resource;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function query(RequestInterface $request)
    {
        try {
            $query = $this->mapper->buildQuery($request);
            $query->limit($this->searchConfig->getResultsLimit());
        } catch (\Exception $e) {
            // fallback engine
            $objectManager = ObjectManager::getInstance();
            return $objectManager->create('Mirasvit\SearchMysql\Model\Adapter')
                ->query($request);
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
     * @param \Magento\Framework\DB\Ddl\Table $table
     * @return array
     * @throws \Zend_Db_Exception
     */
    private function getDocuments(\Magento\Framework\DB\Ddl\Table $table)
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
