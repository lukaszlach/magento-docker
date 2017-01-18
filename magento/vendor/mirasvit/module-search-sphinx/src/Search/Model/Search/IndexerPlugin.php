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


namespace Mirasvit\Search\Model\Search;

use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Mirasvit\Search\Model\Config;
use Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory as IndexCollectionFactory;

class IndexerPlugin
{
    /**
     * @var IndexCollectionFactory
     */
    protected $indexCollectionFactory;

    /**
     * @param IndexCollectionFactory $indexCollectionFactory
     */
    public function __construct(
        IndexCollectionFactory $indexCollectionFactory
    ) {
        $this->indexCollectionFactory = $indexCollectionFactory;
    }

    /**
     * @param Fulltext $fulltext
     * @param \Closure $proceed
     * @param null     $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecuteFull(
        Fulltext $fulltext,
        \Closure $proceed,
        $scope = null
    ) {
        $result = $proceed($scope);

        /** @var \Mirasvit\Search\Model\Index $index */
        foreach ($this->indexCollectionFactory->create() as $index) {
            if ($index->getIsActive()) {
                if ($index->getCode() == 'catalogsearch_fulltext') {
                    $index->setStatus(Config::INDEX_STATUS_READY)
                        ->save();
                } else {
                    $index->getIndexInstance()->reindexAll();
                }
            }
        }

        return $result;
    }
}
