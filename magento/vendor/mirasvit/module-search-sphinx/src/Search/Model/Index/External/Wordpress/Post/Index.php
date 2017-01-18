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


namespace Mirasvit\Search\Model\Index\External\Wordpress\Post;

use Mirasvit\Search\Model\Index\AbstractIndex;
use Mirasvit\Search\Model\Index\IndexerFactory;
use Mirasvit\Search\Model\Index\SearcherFactory;
use Mirasvit\Search\Model\Index\External\Wordpress\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\App\ResourceConnection;

class Index extends AbstractIndex
{
    /**
     * Constructor
     *
     * @param IndexerFactory        $indexerFactory
     * @param SearcherFactory       $searcherFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param ResourceConnection    $resourceConnection
     */
    public function __construct(
        IndexerFactory $indexerFactory,
        SearcherFactory $searcherFactory,
        PostCollectionFactory $postCollectionFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->postCollectionFactory = $postCollectionFactory;
        $this->resourceConnection = $resourceConnection;

        parent::__construct($indexerFactory, $searcherFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Wordpress Blog');
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return __('External');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'external_wordpress_post';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsets()
    {
        return [
            '\Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties\External\Database',
            '\Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties\UrlTemplate',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [
            'post_title'   => __('Post Title'),
            'post_content' => __('Post Content'),
            'post_excerpt' => __('Post Excerpt'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId($attributeCode)
    {
        $attributes = array_keys($this->getAttributes());
        return array_search($attributeCode, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'ID';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildSearchCollection()
    {
        $collection = $this->postCollectionFactory->create(['index' => $this]);

        $this->searcher->joinMatches($collection, 'ID');

        return $collection;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = null, $limit = 100)
    {
        $collection = $this->postCollectionFactory->create(['index' => $this]);

        if ($entityIds) {
            $collection->addFieldToFilter('ID', ['in' => $entityIds]);
        }

        $collection->addFieldToFilter('ID', ['gt' => $lastEntityId])
            ->setPageSize($limit)
            ->setOrder('ID');


        return $collection;
    }

    /**
     * Return new connection to wordpress database
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        if ($this->getModel()->getProperty('db_connection_name')) {
            $connectionName = $this->getModel()->getProperty('db_connection_name');

            $connection = $this->resourceConnection->getConnection($connectionName);

            return $connection;
        }

        return $this->resourceConnection->getConnection();
    }
}
