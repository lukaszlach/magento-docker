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


namespace Mirasvit\SearchSphinx\Model\Adapter\Query\Builder;

use Mirasvit\SearchSphinx\SphinxQL\Expression as QLExpression;
use Mirasvit\SearchSphinx\SphinxQL\SphinxQL;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Mirasvit\Search\Helper\Query as QueryHelper;
use Mirasvit\SearchSphinx\Model\Adapter\Field\FieldInterface;
use Mirasvit\SearchSphinx\Model\Adapter\Field\Resolver;

class Match implements QueryInterface
{
    /**
     * @param Resolver    $resolver
     * @param QueryHelper $queryHelper
     */
    public function __construct(
        Resolver $resolver,
        QueryHelper $queryHelper
    ) {
        $this->resolver = $resolver;
        $this->queryHelper = $queryHelper;
    }

    /**
     * @param SphinxQL              $select
     * @param RequestQueryInterface $query
     * @return SphinxQL
     */
    public function build(
        SphinxQL $select,
        RequestQueryInterface $query
    ) {
        /** @var \Magento\Framework\Search\Request\Query\Match $query */

        $fieldList = [];
        foreach ($query->getMatches() as $match) {
            $fieldList[] = $match['field'];
        }

        $resolvedFieldList = $this->resolver->resolve($fieldList);

        $fieldIds = [];
        $columns = [];
        /** @var \Mirasvit\SearchSphinx\Model\Adapter\Field\Field $field */
        foreach ($resolvedFieldList as $field) {
            if ($field->getType() === FieldInterface::TYPE_FULLTEXT && $field->getAttributeId()) {
                $fieldIds[] = $field->getAttributeId();
            }
            $column = $field->getColumn();
            $columns[$column] = $column;
        }


        $queryValue = $this->queryHelper->buildQuery($query->getValue());
        $matchQuery = $this->buildMatchQuery($queryValue, $select);

        $select->match($columns, new QLExpression($matchQuery));

        return $select;
    }

    /**
     * @param array    $arQuery
     * @param SphinxQL $select
     * @return string
     */
    protected function buildMatchQuery($arQuery, $select)
    {
        $query = '';

        if (!is_array($arQuery) || !count($arQuery)) {
            return '*';
        }

        $result = [];
        foreach ($arQuery as $key => $array) {
            if ($key == 'not like') {
                $result[] = '-' . $this->buildWhere($key, $array, $select);
            } else {
                $result[] = $this->buildWhere($key, $array, $select);
            }
        }

        if (count($result)) {
            $query = '(' . implode(' ', $result) . ')';
        }

        return $query;
    }

    /**
     * @param string   $type
     * @param array    $array
     * @param SphinxQL $select
     * @return array|string
     */
    protected function buildWhere($type, $array, $select)
    {
        if (!is_array($array)) {
            $array = str_replace('/', '\/', $array);
            //            $array = str_replace('-', ' ', $array);
            if (substr($array, 0, 1) == ' ') {
                return '(' . $select->escapeMatch($array) . ')';
            } else {
                return '(*' . $select->escapeMatch($array) . '*)';
            }
        }

        foreach ($array as $key => $subArray) {
            if ($key == 'or') {
                $array[$key] = $this->buildWhere($type, $subArray, $select);
                if (is_array($array[$key])) {
                    $array = '(' . implode(' | ', $array[$key]) . ')';
                }
            } elseif ($key == 'and') {
                $array[$key] = $this->buildWhere($type, $subArray, $select);
                if (is_array($array[$key])) {
                    $array = '(' . implode(' ', $array[$key]) . ')';
                }
            } else {
                $array[$key] = $this->buildWhere($type, $subArray, $select);
            }
        }

        return $array;
    }
}
