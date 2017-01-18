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


namespace Mirasvit\Search\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;
use Mirasvit\Search\Helper\Highlighter as HighlighterHelper;
use Mirasvit\Search\Model\Config;
use Mirasvit\Search\Model\IndexFactory;
use Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory as IndexCollectionFactory;

class Result extends Template
{
    /**
     * @var IndexFactory
     */
    protected $indexFactory;

    /**
     * @var IndexCollectionFactory
     */
    protected $indexCollectionFactory;

    /**
     * @var QueryFactory
     */
    protected $searchQueryFactory;

    /**
     * @var HighlighterHelper
     */
    protected $highlighter;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $indexes;

    /**
     * Constructor
     *
     * @param Context                $context
     * @param IndexCollectionFactory $indexCollectionFactory
     * @param IndexFactory           $indexFactory
     * @param HighlighterHelper      $highlighter
     * @param QueryFactory           $queryFactory
     * @param Config                 $config
     */
    public function __construct(
        Context $context,
        IndexCollectionFactory $indexCollectionFactory,
        IndexFactory $indexFactory,
        HighlighterHelper $highlighter,
        QueryFactory $queryFactory,
        Config $config
    ) {
        $this->indexCollectionFactory = $indexCollectionFactory;
        $this->indexFactory = $indexFactory;
        $this->highlighter = $highlighter;
        $this->config = $config;
        $this->searchQueryFactory = $queryFactory;

        parent::__construct($context);
    }

    /**
     * List of enabled indexes
     *
     * @return \Mirasvit\Search\Model\Index[]
     */
    public function getIndexes()
    {
        if ($this->indexes == null) {
            $result = [];

            $collection = $this->indexCollectionFactory->create()
                ->addFieldToFilter('is_active', 1)
                ->setOrder('position', 'asc');
            /** @var \Mirasvit\Search\Model\Index $index */
            foreach ($collection as $index) {
                if ($this->config->isMultiStoreModeEnabled()
                    && $index->getCode() == 'catalogsearch_fulltext'
                ) {
                    foreach ($this->_storeManager->getStores(false, true) as $code => $store) {
                        if (in_array($store->getId(), $this->config->getEnabledMultiStores())) {
                            $clone = clone $index;
                            $clone->setData('store_id', $store->getId());
                            $clone->setData('store_code', $code);
                            if ($this->_storeManager->getStore()->getId() != $store->getId()) {
                                $clone->setData('title', $store->getName());
                            }
                            $result[] = $clone;
                        }
                    }
                } else {
                    $result[] = $index;
                }
            }

            $this->indexes = $result;
        }

        return $this->indexes;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Current content
     *
     * @return string
     */
    public function getCurrentContent()
    {
        $index = $this->getCurrentIndex();
        $html = $this->getContentBlock($index)->toHtml();

        return $html;
    }

    /**
     * Block for index model
     *
     * @param \Mirasvit\Search\Model\Index $index
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \Exception
     */
    public function getContentBlock($index)
    {
        $block = $this->getChildBlock($index->getCode());

        if (!$block) {
            throw new \Exception(__('Child block %1 not exists', $index->getCode()));
        }

        $block->setIndex($index);

        return $block;
    }

    /**
     * First matched index model
     *
     * @return \Mirasvit\Search\Model\Index
     */
    public function getFirstMatchedIndex()
    {
        foreach ($this->getIndexes() as $index) {
            if ($index->getIndexInstance()->getSearchCollection()->getSize()
                && ($index->getData('store_id') == false
                    || $index->getData('store_id') == $this->getCurrentStore()->getId())
            ) {
                return $index;
            }
        }

        return $this->getIndexes()[0];
    }

    /**
     * Current index model
     *
     * @return \Mirasvit\Search\Model\Index
     */
    public function getCurrentIndex()
    {
        $indexId = $this->getRequest()->getParam('index');

        if ($indexId) {
            foreach ($this->getIndexes() as $index) {
                if ($index->getId() == $indexId) {
                    return $index;
                }
            }

        }

        return $this->getFirstMatchedIndex();
    }

    /**
     * Current index size
     *
     * @return int
     */
    public function getCurrentIndexSize()
    {
        return $this->getCurrentIndex()->getSearchCollection()->getSize();
    }

    /**
     * Index url
     *
     * @param \Mirasvit\Search\Model\Index $index
     *
     * @return string
     */
    public function getIndexUrl($index)
    {
        $query = [
            'index' => $index->getId(),
            'p'     => null
        ];

        if ($index->hasData('store_id')
            && $index->getData('store_id') != $this->getCurrentStore()->getId()
        ) {
            return $this->getUrl('stores/store/switch', [
                '_query' => [
                    '___store' => $index->getData('store_code')
                ]
            ]);
        }

        $params = [
            '_current' => true,
            '_query'   => $query,
        ];

        if ($index->hasData('store_id')) {
            $params['_scope'] = $index->getData('store_id');
        }

        return $this->getUrl('*/*/*', $params);
    }

    /**
     * Save number of results + highlight
     *
     * @param string $html
     *
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $numResults = 0;

        /** @var \Mirasvit\Search\Model\Index $index */
        foreach ($this->getIndexes() as $index) {
            $numResults += $index->getSearchCollection()->getSize();
        }

        $this->searchQueryFactory->get()
            ->saveNumResults($numResults);

        if (!$this->config->isHighlightingEnabled()) {
            return $html;
        }

        $html = $this->highlighter->highlight(
            $html,
            $this->searchQueryFactory->get()->getQueryText(),
            $this->getCurrentIndex()->getCode()
        );

        return $html;
    }
}
