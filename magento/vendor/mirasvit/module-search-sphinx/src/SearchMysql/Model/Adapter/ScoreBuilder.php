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


namespace Mirasvit\SearchMysql\Model\Adapter;

class ScoreBuilder extends \Magento\Framework\Search\Adapter\Mysql\ScoreBuilder
{
    /**
     * @var string
     */
    private $scoreCondition = '';

    /**
     * @var string
     */
    const WEIGHT_FIELD = 'search_weight';

    /**
     * Get column alias for global score query in sql
     *
     * @return string
     */
    public function getScoreAlias()
    {
        return 'score';
    }

    /**
     * Get generated sql condition for global score
     *
     * @return string
     */
    public function build()
    {
        $scoreCondition = $this->scoreCondition;
        $this->clear();
        $scoreAlias = $this->getScoreAlias();

        return new \Zend_Db_Expr("({$scoreCondition}) AS {$scoreAlias}");
    }

    /**
     * Start Query
     *
     * @return void
     */
    public function startQuery()
    {
        $this->addPlus();
        $this->scoreCondition .= '(';
    }

    /**
     * End Query
     *
     * @param float $boost
     * @return void
     */
    public function endQuery($boost)
    {
        if (!empty($this->scoreCondition) && substr($this->scoreCondition, -1) !== '(') {
            $this->scoreCondition .= ") * {$boost}";
        } else {
            $this->scoreCondition .= '0)';
        }
    }

    /**
     * Add Condition for score calculation
     *
     * @param string $score
     * @param bool   $useWeights
     * @return void
     */
    public function addCondition($score, $useWeights = true)
    {
        $this->addPlus();
        $condition = "{$score}";
        if ($useWeights) {
            $condition = "SUM({$condition} * POW(2, " . self::WEIGHT_FIELD . '))';
        }
        $this->scoreCondition .= $condition;
    }

    /**
     * Add Plus sign for Score calculation
     *
     * @return void
     */
    private function addPlus()
    {
        if (!empty($this->scoreCondition) && substr($this->scoreCondition, -1) !== '(') {
            $this->scoreCondition .= ' + ';
        }
    }

    /**
     * Clear score manager
     *
     * @return void
     */
    private function clear()
    {
        $this->scoreCondition = '';
    }
}
