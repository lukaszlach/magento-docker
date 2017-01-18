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


namespace Mirasvit\SearchSphinx\Model\Indexer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;
use Mirasvit\Search\Model\IndexFactory as SearchIndexFactory;
use Mirasvit\SearchSphinx\Model\Engine;
use Mirasvit\Search\Model\Index\Magento\Catalog\Product\Prepare;

class IndexerHandler implements IndexerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @var IndexScopeResolverInterface
     */
    protected $indexScopeResolver;

    /**
     * @var Prepare
     */
    protected $prepare;

    /**
     * @var SearchIndexFactory
     */
    protected $indexFactory;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * IndexerHandler constructor.
     *
     * @param Prepare            $prepare
     * @param Batch              $batch
     * @param IndexScopeResolver $indexScopeResolver
     * @param Engine             $engine
     * @param SearchIndexFactory $indexFactory
     * @param array              $data
     * @param int                $batchSize
     */
    public function __construct(
        Prepare $prepare,
        Batch $batch,
        IndexScopeResolver $indexScopeResolver,
        Engine $engine,
        SearchIndexFactory $indexFactory,
        array $data,
        $batchSize = 100
    ) {
        $this->prepare = $prepare;
        $this->indexScopeResolver = $indexScopeResolver;
        $this->batch = $batch;
        $this->data = $data;
        $this->fields = [];
        $this->engine = $engine;
        $this->indexFactory = $indexFactory;

        $this->prepareFields();

        $this->batchSize = $batchSize;
    }

    /**
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param \Traversable                                  $documents
     * @return void
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $index = $this->indexFactory->create()->load($this->getIndexName());
        $indexName = $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);

        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            if ($this->getIndexName() == 'catalogsearch_fulltext') {
                $this->prepare->prepareBatchDocuments($batchDocuments);
            }

            $this->engine->saveDocuments($index, $indexName, $batchDocuments);
        }
    }

    /**
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param \Traversable                                  $documents
     * @return void
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $index = $this->indexFactory->create()->load($this->getIndexName());
        $indexName = $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);

        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            $this->engine->deleteDocuments($index, $indexName, $batchDocuments);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cleanIndex($dimensions)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * @return string
     */
    private function getIndexName()
    {
        return $this->data['indexer_id'];
    }

    /**
     * @return void
     */
    private function prepareFields()
    {
        foreach ($this->data['fieldsets'] as $fieldset) {
            $this->fields = array_merge($this->fields, $fieldset['fields']);
        }
    }
}
