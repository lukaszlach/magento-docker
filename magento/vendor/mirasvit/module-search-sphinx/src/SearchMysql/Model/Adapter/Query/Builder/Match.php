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


namespace Mirasvit\SearchMysql\Model\Adapter\Query\Builder;

use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\Field\FieldInterface;
use Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface;
use Magento\Framework\Search\Adapter\Mysql\ScoreBuilder;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\Search\Request\Query\BoolExpression;
use Magento\Framework\DB\Helper as DbHelper;
use Mirasvit\Search\Helper\Query as QueryHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Match extends \Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match
{
    const SPECIAL_CHARACTERS = '-+~/\\<>\'":*$#@()!,.?`=';

    const MINIMAL_CHARACTER_LENGTH = 3;

    /**
     * @var string[]
     */
    private $replaceSymbols = [];

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var Fulltext
     */
    private $fulltextHelper;

    /**
     * @var DbHelper
     */
    protected $dbHelper;

    /**
     * @var QueryHelper
     */
    protected $queryHelper;

    /**
     * @var string
     */
    private $fulltextSearchMode;

    /**
     * @param ResolverInterface $resolver
     * @param Fulltext          $fulltextHelper
     * @param DbHelper          $dbHelper
     * @param QueryHelper       $queryHelper
     * @param string            $fulltextSearchMode
     */
    public function __construct(
        ResolverInterface $resolver,
        Fulltext $fulltextHelper,
        DbHelper $dbHelper,
        QueryHelper $queryHelper,
        $fulltextSearchMode = Fulltext::FULLTEXT_MODE_BOOLEAN
    ) {
        $this->resolver = $resolver;
        $this->replaceSymbols = str_split(self::SPECIAL_CHARACTERS, 1);
        $this->fulltextHelper = $fulltextHelper;
        $this->queryHelper = $queryHelper;
        $this->dbHelper = $dbHelper;
        $this->fulltextSearchMode = $fulltextSearchMode;

        parent::__construct($resolver, $fulltextHelper, $fulltextSearchMode);
    }

    /**
     * {@inheritdoc}
     */
    public function build(
        ScoreBuilder $scoreBuilder,
        Select $select,
        RequestQueryInterface $query,
        $conditionType
    ) {
        /** @var $query \Magento\Framework\Search\Request\Query\Match */

        $fieldList = [];
        foreach ($query->getMatches() as $match) {
            $fieldList[] = $match['field'];
        }
        $resolvedFieldList = $this->resolver->resolve($fieldList);

        $fieldIds = [];
        $columns = [];
        foreach ($resolvedFieldList as $field) {
            if ($field->getType() === FieldInterface::TYPE_FULLTEXT && $field->getAttributeId()) {
                $fieldIds[] = $field->getAttributeId();
            }
            $column = $field->getColumn();
            $columns[$column] = $column;
        }

        $exactMatchQuery = $this->getExactMatchQuery($columns, $query->getValue());

        $scoreQuery = $this->getScoreQuery($columns, $query->getValue());
        $scoreBuilder->addCondition(new \Zend_Db_Expr($scoreQuery), true);

        if ($query->getValue() != $this->queryHelper->singularize($query->getValue())) {
            $scoreQuery = $this->getScoreQuery($columns, $this->queryHelper->singularize($query->getValue()));
            $scoreBuilder->addCondition(new \Zend_Db_Expr($scoreQuery), true);
        }

        if ($fieldIds) {
            $select->where(sprintf('search_index.attribute_id IN (%s)', implode(',', $fieldIds)));
        }

        $select
            ->having(new \Zend_Db_Expr($exactMatchQuery))
            ->group('entity_id');

        return $select;
    }

    /**
     * @param array  $columns
     * @param string $queryValue
     * @return string
     */
    public function getExactMatchQuery($columns, $queryValue)
    {
        $queryValue = $this->queryHelper->buildQuery($queryValue);

        if (!is_array($queryValue)) {
            return '';
        }

        $result = [1];
        foreach ($queryValue as $key => $array) {
            $result[] = $this->buildWhere($key, $array, $columns);
        }

        $where = '(' . implode(' AND ', $result) . ')';

        return $where;
    }

    /**
     * @param string $type
     * @param array  $array
     * @param array  $columns
     * @return array|string
     */
    protected function buildWhere($type, $array, $columns)
    {
        if (!is_array($array)) {
            $likes = [];
            foreach ($columns as $attribute) {
                $attribute = new \Zend_Db_Expr('GROUP_CONCAT(' . $attribute . ')');
                $likes[] = $this->dbHelper->getCILike($attribute, $array, ['position' => 'any']);
            }

            return '(' . implode(' OR ', $likes) . ')';
        }

        foreach ($array as $key => $subArray) {
            if ($key == 'or') {
                $array[$key] = $this->buildWhere($type, $subArray, $columns);
                if (is_array($array[$key])) {
                    $array = '(' . implode(' OR ', $array[$key]) . ')';
                }
            } elseif ($key == 'and') {
                $array[$key] = $this->buildWhere($type, $subArray, $columns);
                if (is_array($array[$key])) {
                    $array = '(' . implode(' AND ', $array[$key]) . ')';
                }
            } else {
                $array[$key] = $this->buildWhere($type, $subArray, $columns);
            }
        }

        return $array;
    }

    /**
     * @param array $columns
     * @param string $query
     * @return string
     */
    public function getScoreQuery($columns, $query)
    {
        $cases = [];
        $fullCases = [];

        $words = preg_split('#\s#siu', $query, null, PREG_SPLIT_NO_EMPTY);

        foreach ($columns as $column) {
            $cases[5][] = $this->dbHelper->getCILike($column, ' ' . $query . ' ');
        }

        foreach ($words as $word) {
            foreach ($columns as $column) {
                $cases[3][] = $this->dbHelper->getCILike($column, ' ' . $word . ' ', ['position' => 'any']);
                $cases[2][] = $this->dbHelper->getCILike($column, $word, ['position' => 'any']);
            }
        }

        foreach ($words as $word) {
            foreach ($columns as $column) {
                $e = '(LENGTH(' . $column . ')';
                $e .= '- LOCATE("' . addslashes($word) . '", ' . $column . ')) / LENGTH(' . $column . ')';
                $locate = new \Zend_Db_Expr($e);
                $cases[$locate->__toString()][] = $locate;
            }
        }

        foreach ($cases as $weight => $conditions) {
            foreach ($conditions as $condition) {
                $fullCases[] = 'CASE WHEN ' . $condition . ' THEN ' . $weight . ' ELSE 0 END';
            }
        }

        if (count($fullCases)) {
            $select = '(' . implode('+', $fullCases) . ')';
        } else {
            $select = '0';
        }

        return $select;
    }

    /**
     * @param string $queryValue
     * @param string $conditionType
     * @return string
     */
    protected function prepareFastQuery($queryValue, $conditionType)
    {
        $queryValue = str_replace($this->replaceSymbols, ' ', $queryValue);

        $stringPrefix = '';
        if ($conditionType === BoolExpression::QUERY_CONDITION_MUST) {
            $stringPrefix = '+';
        } elseif ($conditionType === BoolExpression::QUERY_CONDITION_NOT) {
            $stringPrefix = '-';
        }

        $queryValues = explode(' ', $queryValue);

        foreach ($queryValues as $queryKey => $queryValue) {
            if (empty($queryValue)) {
                unset($queryValues[$queryKey]);
            } else {
                $stringSuffix = '*';
                $queryValues[$queryKey] = $stringPrefix . $queryValue . $stringSuffix;
            }
        }

        $queryValue = implode(' ', $queryValues);

        return $queryValue;
    }
}
