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


namespace Mirasvit\Search\Model\Index\Mirasvit\Blog\Post;

use Mirasvit\Search\Model\Index\AbstractIndex;
use Magento\Framework\App\ObjectManager;

class Index extends AbstractIndex
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Blog MX')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return __('Mirasvit')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'mirasvit_blog_post';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [
            'name'             => __('Name'),
            'content'          => __('Content'),
            'short_content'    => __('Short Content'),
            'meta_title'       => __('Meta Title'),
            'meta_keywords'    => __('Meta Keywords'),
            'meta_description' => __('Meta Description'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'entity_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildSearchCollection()
    {
        /** @var \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory $collection */
        $collectionFactory = ObjectManager::getInstance()
            ->create('Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory');

        $collection = $collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addVisibilityFilter();

        $this->searcher->joinMatches($collection, 'e.entity_id');

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = null, $limit = 100)
    {
        /** @var \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory $collection */
        $collectionFactory = ObjectManager::getInstance()
            ->create('Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory');

        $collection = $collectionFactory->create()
            ->addAttributeToSelect(array_keys($this->getAttributes()))
            ->addVisibilityFilter();

        if ($entityIds) {
            $collection->addFieldToFilter('entity_id', ['in' => $entityIds]);
        }

        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId])
            ->setPageSize($limit)
            ->setOrder('entity_id');

        return $collection;
    }
}
