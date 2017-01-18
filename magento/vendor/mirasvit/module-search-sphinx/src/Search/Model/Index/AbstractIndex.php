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


namespace Mirasvit\Search\Model\Index;

use Magento\Framework\DataObject;
use Mirasvit\Search\Model\Config;
use Mirasvit\Search\Model\Index;

/**
 * {@inheritdoc}
 *
 * @method $this setModel(Index $index)
 * @method Index getModel()
 * @method array getAttributes()
 */
abstract class AbstractIndex extends DataObject
{
    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * @var Searcher
     */
    protected $searcher;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $searchCollection;

    /**
     * Constructor
     *
     * @param IndexerFactory  $indexerFactory
     * @param SearcherFactory $searcherFactory
     */
    public function __construct(
        IndexerFactory $indexerFactory,
        SearcherFactory $searcherFactory
    ) {
        $this->indexer = $indexerFactory->create()->setIndex($this);
        $this->searcher = $searcherFactory->create()->setIndex($this);

        parent::__construct();
    }

    /**
     * Index name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Index group name
     *
     * @return string
     */
    abstract public function getGroup();

    /**
     * Index code
     *
     * @return string
     */
    abstract public function getCode();

    /**
     * To String
     *
     * @param string $format
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toString($format = '')
    {
        return $this->getGroup() . ' / ' . $this->getName();
    }

    /**
     * Search collection
     *
     * @return ?
     */
    abstract protected function buildSearchCollection();

    /**
     * Indexer model
     *
     * @return Indexer
     */
    public function getIndexer()
    {
        return $this->indexer;
    }

    /**
     * Fieldsets (names of classes)
     *
     * @return array
     */
    public function getFieldsets()
    {
        return [];
    }

    /**
     * Search collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getSearchCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

        /** @var \Magento\Store\Model\App\Emulation $emulation */
        $emulation = $objectManager->create('Magento\Store\Model\App\Emulation');

        if (!$this->searchCollection) {
            $isEmulation = false;
            if ($this->getData('store_id')
                && $this->getData('store_id') != $storeManager->getStore()->getId()
            ) {
                $emulation->startEnvironmentEmulation($this->getData('store_id'));
                $isEmulation = true;
            }

            $this->searchCollection = $this->buildSearchCollection();

            if ($isEmulation) {
                $this->searchCollection->getSize();
                // get size before switch to default store
                $emulation->stopEnvironmentEmulation();
            }
        }

        return $this->searchCollection;
    }

    /**
     * Wights of attributes
     *
     * @return array
     */
    public function getAttributeWeights()
    {
        $weights = unserialize($this->getData('attributes_serialized'));
        $weights = is_array($weights) ? $weights : [];

        return $weights;
    }

    /**
     * Attribute id
     *
     * @param string $attributeCode
     * @return int
     */
    public function getAttributeId($attributeCode)
    {
        $attributes = array_keys($this->getAttributes());

        return array_search($attributeCode, $attributes);
    }

    /**
     * Reindex all stores
     *
     * @return bool
     */
    public function reindexAll()
    {
        $this->indexer->reindexAll();

        $this->getModel()
            ->setStatus(Config::INDEX_STATUS_READY)
            ->save();

        return true;
    }

    /**
     * Callback on model save after
     *
     * @return $this
     */
    public function afterModelSave()
    {
        return $this;
    }

    /**
     * Attribute code by id
     *
     * @param int $attributeId
     * @return string
     */
    public function getAttributeCode($attributeId)
    {
        return array_keys($this->getAttributes())[$attributeId];
    }

    /**
     * Searchable entities (fro reindex)
     *
     * @param int        $storeId
     * @param null|array $entityIds
     * @param int        $lastEntityId
     * @param int        $limit
     * @return \Magento\Framework\Data\Collection\AbstractDb|array
     */
    abstract public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = 0, $limit = 100);

    /**
     * Table primary key
     *
     * @return string
     */
    abstract public function getPrimaryKey();
}
