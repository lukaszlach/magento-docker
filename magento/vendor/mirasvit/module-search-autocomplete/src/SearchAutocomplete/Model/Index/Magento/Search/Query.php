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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.0.36
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchAutocomplete\Model\Index\Magento\Search;

use Mirasvit\SearchAutocomplete\Model\Index\AbstractIndex;
use Magento\Framework\UrlFactory;

class Query extends AbstractIndex
{
    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * Query constructor.
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        UrlFactory $urlFactory
    ) {
        $this->urlFactory = $urlFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->collection->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];

        /** @var \Magento\Search\Model\Query $query */
        foreach ($this->collection as $query) {
            $url = $this->urlFactory->create();
            $url->setQueryParam('q', $query->getQueryText());
            $url = $url->getUrl('catalogsearch/result');

            $key = strtolower(trim($query->getQueryText()));

            $items[$key] = [
                'query_text'  => $query->getQueryText(),
                'num_results' => $query->getNumResults(),
                'popularity'  => $query->getPopularity(),
                'url'         => $url
            ];
        }


        return array_values($items);
    }
}
