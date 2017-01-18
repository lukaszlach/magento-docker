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


namespace Mirasvit\Search\Model\Index\Magento\Cms\Page;

use Mirasvit\Search\Model\Index\AbstractIndex;
use Mirasvit\Search\Model\Index\IndexerFactory;
use Mirasvit\Search\Model\Index\SearcherFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Cms\Model\Template\FilterProvider as CmsFilterProvider;
use Magento\Email\Model\TemplateFactory as EmailTemplateFactory;

/**
 * @method array getIgnoredPages()
 */
class Index extends AbstractIndex
{
    /**
     * @var PageCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CmsFilterProvider
     */
    protected $cmsFilterProvider;

    /**
     * @var EmailTemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * Constructor
     *
     * @param PageCollectionFactory $collectionFactory
     * @param CmsFilterProvider     $cmsFilterProvider
     * @param IndexerFactory        $indexer
     * @param SearcherFactory       $searcher
     */
    public function __construct(
        PageCollectionFactory $collectionFactory,
        CmsFilterProvider $cmsFilterProvider,
        EmailTemplateFactory $emailTemplateFactory,
        IndexerFactory $indexer,
        SearcherFactory $searcher
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->cmsFilterProvider = $cmsFilterProvider;
        $this->emailTemplateFactory = $emailTemplateFactory;

        parent::__construct($indexer, $searcher);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Cms Page')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return __('Magento')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'magento_cms_page';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsets()
    {
        return ['\Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties\CmsPage'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [
            'title'            => __('Title'),
            'content'          => __('Content'),
            'content_heading'  => __('Content Heading'),
            'meta_keywords'    => __('Meta Keywords'),
            'meta_description' => __('Meta Description'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'page_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildSearchCollection()
    {
        $collection = $this->collectionFactory->create();

        $this->searcher->joinMatches($collection, 'main_table.page_id');

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = null, $limit = 100)
    {
        $collection = $this->collectionFactory->create()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);

        $ignored = $this->getModel()->getProperty('ignored_pages');
        if (is_array($ignored) && count($ignored)) {
            $collection->addFieldToFilter('identifier', ['nin' => $ignored]);
        }

        if ($entityIds) {
            $collection->addFieldToFilter('page_id', $entityIds);
        }

        $collection
            ->addFieldToFilter('page_id', ['gt' => $lastEntityId])
            ->setPageSize($limit)
            ->setOrder('page_id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();


        try {
            /** @var \Magento\Store\Model\App\Emulation $emulation */
            $emulation = $objectManager->create('Magento\Store\Model\App\Emulation');
            $emulation->startEnvironmentEmulation($storeId, true);

            /** @var \Magento\Cms\Model\Page $page */
            foreach ($collection as $page) {
                $template = $this->emailTemplateFactory->create();
                $template->emulateDesign($storeId);
                $template->setTemplateText($page->getContent())
                    ->setIsPlain(false);
                $template->setTemplateFilter($this->cmsFilterProvider->getPageFilter());
                $html = $template->getProcessedTemplate([]);

                $page->setContent($page->getContent() . $html);
            }
            $emulation->stopEnvironmentEmulation();
        } catch (\Exception $e) {}

        return $collection;
    }
}
