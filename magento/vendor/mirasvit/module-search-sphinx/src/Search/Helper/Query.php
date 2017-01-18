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


namespace Mirasvit\Search\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Mirasvit\Search\Model\Config;

class Query extends AbstractHelper
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mirasvit\Search\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Search\Helper\Inflect\En
     */
    protected $inflect;

    /**
     * @var \Mirasvit\Search\Model\Synonym
     */
    protected $synonym;

    /**
     * @var \Mirasvit\Search\Model\Stopword
     */
    protected $stopword;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Mirasvit\Search\Model\Config              $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mirasvit\Search\Helper\Inflect\En         $inflect
     * @param \Mirasvit\Search\Model\Synonym             $synonym
     * @param \Mirasvit\Search\Model\Stopword            $stopword
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Mirasvit\Search\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Search\Helper\Inflect\En $inflect,
        \Mirasvit\Search\Model\Synonym $synonym,
        \Mirasvit\Search\Model\Stopword $stopword
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->inflect = $inflect;
        $this->synonym = $synonym;
        $this->stopword = $stopword;

        parent::__construct($context);
    }

    /**
     * @param string     $originalQuery
     * @param bool|false $inverseNot
     *
     * @return array
     */
    public function buildQuery($originalQuery, $inverseNot = false)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $cacheKey = $originalQuery . $storeId;

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $query = strtolower($originalQuery);
        // required if synonym contains more 1 word
        $query = ' ' . $query . ' ';

        $result = [];

        $replaceWords = $this->config->getReplaceWords();

        foreach ($replaceWords as $replacement) {
            $query = str_replace(' ' . $replacement['from'] . ' ', ' ' . $replacement['to'] . ' ', $query);
        }

        $arWords = preg_split('#\s#siu', $query, null, PREG_SPLIT_NO_EMPTY);

        $logic = 'like';

        $arSynonyms = $this->synonym->getSynonymsByWord($arWords, $storeId);

        foreach ($arWords as $word) {
            if (in_array($word, $this->config->getNotWords())) {
                $logic = 'not like';
                continue;
            }

            if ($this->stopword->isStopWord($word, $storeId)) {
                continue;
            }

            $wordArr = [];
            $this->addWord($wordArr, $word);

            if ($logic == 'like') {
                $longTail = $this->longTail($word);

                $this->addWord($wordArr, $longTail);

                $singular = $this->inflect->singularize($word);
                $this->addWord($wordArr, $singular);

                if (isset($arSynonyms[$word])) {
                    # for synonyms we always disable wildcards
                    $this->addWord($wordArr, $arSynonyms[$word], Config::WILDCARD_DISABLED);
                }

                $template = 'and';
                $result[$logic][$template][$word] = ['or' => $wordArr];
            } else {
                if (!$inverseNot) {
                    $result[$logic]['and'][$word] = ['and' => $wordArr];
                } else {
                    $result[$logic]['or'][$word] = ['and' => $wordArr];
                }
            }
        }

        $this->cache[$cacheKey] = $result;

        return $result;
    }

    /**
     * Apply long tail expression for word
     *
     * @param string $word
     *
     * @return string
     */
    public function longTail($word)
    {
        $expressions = $this->config->getLongTailExpressions();

        foreach ($expressions as $expr) {
            $matches = null;
            preg_match_all($expr['match_expr'], $word, $matches);

            foreach ($matches[0] as $math) {
                $math = preg_replace($expr['replace_expr'], $expr['replace_char'], $math);
                if ($math) {
                    $word = $math;
                }
            }
        }

        return $word;
    }


    /**
     * @param array    &$to
     * @param array    $words
     * @param int|null $wildcard
     * @return void
     */
    protected function addWord(&$to, $words, $wildcard = null)
    {
        $exceptions = $this->config->getWildcardExceptions();
        if ($wildcard == null) {
            $wildcard = $this->config->getWildcardMode();
        }

        if (!is_array($words)) {
            $words = [$words];
        }

        foreach ($words as $word) {
            if ($wildcard == Config::WILDCARD_PREFIX) {
                $word = $word . ' ';
            } elseif ($wildcard == Config::WILDCARD_SUFFIX) {
                $word = ' ' . $word;
            } elseif ($wildcard == Config::WILDCARD_DISABLED || in_array($word, $exceptions)) {
                $word = ' ' . $word . ' ';
            }

            if (trim($word) !== '') {
                $to[$word] = $word;
            }
        }

        ksort($to);
    }

    /**
     * @param string $word
     * @return string
     */
    public function singularize($word)
    {
        return $this->inflect->singularize($word);
    }
}
