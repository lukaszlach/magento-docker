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

use Mirasvit\Search\Model\Index\Pool as IndexPool;

class RequestGenerator
{
    /**
     * @var IndexPool
     */
    protected $indexPool;

    /**
     * @param IndexPool $indexPool
     */
    public function __construct(
        IndexPool $indexPool
    ) {
        $this->indexPool = $indexPool;
    }

    /**
     * Generate dynamic fields requests
     *
     * @return array
     */
    public function generate()
    {
        $requests = [];

        foreach ($this->indexPool->getAvailableIndexes() as $index) {
            $requests[$index->getCode()] = $this->generateQuickSearchRequest($index);
        }

        return $requests;
    }

    /**
     * @param \Mirasvit\Search\Model\Index\AbstractIndex $index
     * @return array
     */
    private function generateQuickSearchRequest($index)
    {
        $request = [];
        $request['dimensions']['scope']['name'] = 'scope';
        $request['dimensions']['scope']['value'] = 'default';

        $request['query'] = $index->getCode();
        $request['index'] = $index->getCode();
        $request['from'] = '0';
        $request['size'] = '100';

        $request['filters'] = [];
        $request['aggregations'] = [];

        $code = $index->getCode();
        $request['queries'][$code]['type'] = 'boolQuery';
        $request['queries'][$code]['name'] = $code;
        $request['queries'][$code]['boost'] = 1;
        $request['queries'][$code]['queryReference'] = [
            [
                'clause' => 'should',
                'ref'    => 'search_query'
            ]
        ];

        $request['queries']['search_query']['name'] = $index->getCode();
        $request['queries']['search_query']['type'] = 'matchQuery';
        $request['queries']['search_query']['value'] = '$search_term$';

        $request['queries']['search_query']['match'][] = [
            'field' => '*',
            'boost' => 10
        ];

        return $request;
    }
}
