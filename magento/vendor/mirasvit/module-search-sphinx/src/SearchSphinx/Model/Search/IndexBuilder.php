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


namespace Mirasvit\SearchSphinx\Model\Search;

use Magento\Framework\Search\RequestInterface;
use SphinxQL\SphinxQL;
use Magento\Framework\App\ResourceConnection;

class IndexBuilder implements \Magento\Framework\Search\Adapter\Mysql\IndexBuilderInterface
{
    /**
     * @param ResourceConnection                              $resource
     * @param \Mirasvit\SearchSphinx\Model\Adapter\MapperQL   $mapperQL
     * @param \Magento\CatalogSearch\Model\Search\TableMapper $tableMapper
     */
    public function __construct(
        ResourceConnection $resource,
        \Mirasvit\SearchSphinx\Model\Adapter\MapperQL $mapperQL,
        \Magento\CatalogSearch\Model\Search\TableMapper $tableMapper
    ) {
        $this->resource = $resource;
        $this->mapperQL = $mapperQL;
        $this->tableMapper = $tableMapper;
    }

    /**
     * Build index query
     *
     * @param RequestInterface $request
     *
     * @return \Mirasvit\SearchSphinx\SphinxQL\SphinxQL
     */
    public function build(RequestInterface $request)
    {
        $table = $this->mapperQL->buildQuery($request);

        $select = $this->resource->getConnection()->select()
            ->from(
                ['search_index' => $table->getName()],
                ['entity_id' => 'entity_id', 'score' => 'score']
            );

        return $select;
    }
}
