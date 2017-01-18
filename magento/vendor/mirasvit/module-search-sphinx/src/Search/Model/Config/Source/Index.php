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


namespace Mirasvit\Search\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Search\Model\Index\Pool;
use Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory as IndexCollectionFactory;

class Index implements ArrayInterface
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var IndexCollectionFactory
     */
    protected $indexCollection;

    /**
     * Constructor
     *
     * @param Pool                   $pool
     * @param IndexCollectionFactory $indexCollectionFactory
     */
    public function __construct(
        Pool $pool,
        IndexCollectionFactory $indexCollectionFactory
    ) {
        $this->pool = $pool;
        $this->indexCollection = $indexCollectionFactory->create();
    }

    /**
     * To option array
     *
     * @param bool $onlyUnused
     * @return array
     */
    public function toOptionArray($onlyUnused = false)
    {
        $options = [];
        foreach ($this->pool->getAvailableIndexes() as $index) {
            $code = $index->getCode();
            if (!$onlyUnused || !$this->indexCollection->getItemByColumnValue('code', $code)) {
                $options[$code] = $index->toString();
            }
        }

        return $options;
    }
}
