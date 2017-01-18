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


namespace Mirasvit\SearchAutocomplete\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var QueryCollectionFactory
     */
    protected $queryCollectionFactory;

    /**
     * @param ScopeConfigInterface   $scopeConfig
     * @param QueryCollectionFactory $queryCollectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        QueryCollectionFactory $queryCollectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->queryCollectionFactory = $queryCollectionFactory;
    }

    /**
     * Is show product image
     *
     * @return bool
     */
    public function isShowImage()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_image');
    }

    /**
     * Is show product rating
     *
     * @return bool
     */
    public function isShowRating()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_rating');
    }

    /**
     * Is show product description
     *
     * @return bool
     */
    public function isShowShortDescription()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_description');
    }

    /**
     * Product description length
     *
     * @return int
     */
    public function getShortDescriptionLen()
    {
        return 100;
    }

    /**
     * Is show product price
     *
     * @return bool
     */
    public function isShowPrice()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_price');
    }

    /**
     * Delay before start search (miliseconds)
     *
     * @return int
     */
    public function getDelay()
    {
        return intval($this->scopeConfig->getValue('searchautocomplete/general/delay'));
    }

    /**
     * Minimum number of chars to start search
     *
     * @return int
     */
    public function getMinChars()
    {
        return intval($this->scopeConfig->getValue('searchautocomplete/general/min_chars'));
    }

    /**
     * Search indexes configuration
     *
     * @return array
     */
    public function getIndexConfiguration()
    {
        $indexes = @unserialize($this->scopeConfig->getValue('searchautocomplete/general/index'));

        return $indexes;
    }

    /**
     * Additional (custom) css styles
     *
     * @return string
     */
    public function getCssStyles()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/appearance/css');
    }

    /**
     * Get search index option value
     *
     * @param string $code
     * @param string $option
     * @return bool|string
     */
    public function getIndexOptionValue($code, $option)
    {
        if (isset($this->getIndexConfiguration()[$code]) && isset($this->getIndexConfiguration()[$code][$option])) {
            return $this->getIndexConfiguration()[$code][$option];
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isShowPopularSearches()
    {
        return $this->scopeConfig->getValue('searchautocomplete/popular/enabled');
    }

    /**
     * @return array
     */
    public function getDefaultPopularSearches()
    {
        $result = $this->scopeConfig->getValue('searchautocomplete/popular/default');
        $result = array_filter(array_map('trim', explode(',', $result)));

        return $result;
    }

    /**
     * @return array
     */
    public function getIgnoredSearches()
    {
        $result = $this->scopeConfig->getValue('searchautocomplete/popular/ignored');
        $result = array_filter(array_map('strtolower', array_map('trim', explode(',', $result))));

        return $result;
    }

    /**
     * @return array
     */
    public function getPopularSearches()
    {
        $result = $this->getDefaultPopularSearches();

        if (!count($result)) {
            $ignored = $this->getIgnoredSearches();

            $collection = $this->queryCollectionFactory->create()
                ->setPopularQueryFilter()
                ->setPageSize(20);
            /** @var \Magento\Search\Model\Query $query */
            foreach ($collection as $query) {
                $text = $query->getName();
                $isIgnored = false;
                foreach ($ignored as $word) {
                    if (strpos(strtolower($text), $word) !== false) {
                        $isIgnored = true;
                        break;
                    }
                }

                if (!$isIgnored) {
                    $result[] = $text;
                }
            }
        }

        $result = array_map('ucfirst', $result);

        return $result;
    }
}
