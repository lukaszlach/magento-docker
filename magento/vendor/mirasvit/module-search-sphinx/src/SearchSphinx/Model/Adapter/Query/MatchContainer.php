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


namespace Mirasvit\SearchSphinx\Model\Adapter\Query;

use Magento\Framework\Search\Request\QueryInterface;

class MatchContainer
{
    /**
     * @var QueryInterface
     */
    private $request;
    /**
     * @var string
     */
    private $conditionType;

    /**
     * @param QueryInterface $request
     * @param string         $conditionType
     * @internal param string $name
     */
    public function __construct(QueryInterface $request, $conditionType)
    {
        $this->request = $request;
        $this->conditionType = $conditionType;
    }

    /**
     * @return QueryInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }
}
