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
 * @package   mirasvit/module-misspell
 * @version   1.0.7
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Misspell\Model;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Misspell\Helper\Text as TextHelper;

/**
 * Perform spell correct for given phrase
 */
class Misspell
{
    /**
     * @var array
     */
    protected $diffs;

    /**
     * @var array
     */
    protected $keys;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var TextHelper
     */
    protected $damerau;

    /**
     * @var TextHelper
     */
    protected $text;

    /**
     * Constructor
     *
     * @param ResourceConnection $resource
     * @param TextHelper         $textHelper
     * @param Damerau            $damerau
     */
    public function __construct(
        ResourceConnection $resource,
        TextHelper $textHelper,
        Damerau $damerau
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->damerau = $damerau;
        $this->text = $textHelper;
    }

    /**
     * Return spelled string
     *
     * @param string $baseQuery
     * @return string
     */
    public function getSuggest($baseQuery)
    {
        $this->diffs = [];
        $this->keys = [];
        $final = [];

        $baseQuery = $this->text->cleanString($baseQuery);
        $queries = $this->text->splitWords($baseQuery);

        foreach ($queries as $query) {
            $len = $this->text->strlen($query);

            if ($len < $this->text->getGram() || is_numeric($query)) {
                $final[] = $query;
                continue;
            }

            $result = $this->getBestMatch($query);
            $keyword = $result['keyword'];

            $this->split($query, '', $query);
            $splitKeyword = '';

            if (count($this->diffs)) {
                arsort($this->diffs);
                $keys = array_keys($this->diffs);
                $key = $keys[0];
                $splitKeyword = $this->keys[$key];
            }

            $basePer = $this->damerau->similarity($query, $keyword);
            $splitPer = $this->damerau->similarity($query, $splitKeyword);

            if ($basePer > $splitPer) {
                $final[] = $keyword;
            } else {
                $final[] = $splitKeyword;
            }
        }

        $result = implode(' ', $final);

        if ($this->damerau->similarity($result, $baseQuery) < 50) {
            $result = '';
        }

        return $result;
    }

    /**
     * Split string
     *
     * @param string $query
     * @param string $prefix
     * @param string $base
     * @return bool
     */
    protected function split($query, $prefix = '', $base = '')
    {
        $len = $this->text->strlen($query);

        if ($len > 20) {
            return false;
        }

        for ($i = $this->text->getGram(); $i <= $len - $this->text->getGram() + 1; $i++) {
            $a = $this->text->substr($query, 0, $i);
            $b = $this->text->substr($query, $i);

            $aa = $this->getBestMatch($a);
            $bb = $this->getBestMatch($b);

            $key = $a . '|' . $b;

            if ($prefix) {
                $key = $prefix . '|' . $key;
            }

            $this->keys[$key] = '';
            if ($prefix) {
                $this->keys[$key] = $prefix . ' ';
            }
            $this->keys[$key] .= $aa['keyword'] . ' ' . $bb['keyword'];


            $this->diffs[$key] = ($this->damerau->similarity($base, $this->keys[$key]) + $aa['diff'] + $bb['diff']) / 3;

            if ($prefix) {
                $kwd = $prefix . '|' . $aa['keyword'];
            } else {
                $kwd = $aa['keyword'];
            }

            if ($aa['diff'] > 50) {
                $this->split($b, $kwd, $query);
            }

        }

        return null;
    }

    /**
     * Return best match (from database)
     *
     * @param string $query
     * @return array
     */
    public function getBestMatch($query)
    {
        $query = trim($query);

        if (!$query) {
            return ['keyword' => $query, 'diff' => 100];;
        }

        $len = intval($this->text->strlen($query));
        $trigram = $this->text->getTrigram($this->text->strtolower($query));

        $tableName = $this->resource->getTableName('mst_misspell_index');

        $select = $this->connection->select();
        $relevance = '(-ABS(LENGTH(keyword) - ' . $len . ') + MATCH (trigram) AGAINST("' . $trigram . '"))';
        $relevancy = new \Zend_Db_Expr($relevance . ' + frequency AS relevancy');
        $select->from($tableName, ['keyword', $relevancy, 'frequency'])
            ->order('relevancy desc')
            ->limit(10);
        
        $keywords = $this->connection->fetchAll($select);

        $maxFreq = 0.0001;
        foreach ($keywords as $keyword) {
            $maxFreq = max($keyword['frequency'], $maxFreq);
        }

        $preResults = [];
        foreach ($keywords as $keyword) {
            $preResults[$keyword['keyword']] = $this->damerau->similarity($query, $keyword['keyword'])
                + $keyword['frequency'] * (10 / $maxFreq);
        }
        arsort($preResults);

        $keys = array_keys($preResults);

        if (count($keys) > 0) {
            $keyword = $keys[0];
            $keyword = $this->toSameRegister($keyword, $query);
            $diff = $preResults[$keys[0]];
            $result = ['keyword' => $keyword, 'diff' => $diff];
        } else {
            $result = ['keyword' => $query, 'diff' => 100];
        }

        return $result;
    }

    /**
     * Convert $str to same register with $base
     *
     * @param string $str
     * @param string $base
     * @return string
     */
    protected function toSameRegister($str, $base)
    {
        $minLen = min($this->text->strlen($base), $this->text->strlen($str));

        for ($i = 0; $i < $minLen; $i++) {
            $chr = $this->text->substr($base, $i, 1);

            if ($chr != $this->text->strtolower($chr)) {
                $chrN = $this->text->substr($str, $i, 1);
                $chrN = strtoupper($chrN);
                $str = substr_replace($str, $chrN, $i, 1);
            }
        }

        return $str;
    }
}
