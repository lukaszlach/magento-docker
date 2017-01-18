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
//@codingStandardsIgnoreFile
//@todo find external library
class Highlighter extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $conditions = [
        ['(<a[^>]*>)', '(<\/a>)']
    ];

    /**
     * Highlight search term on the search result pages.
     *
     * @param $block
     * @param $transport
     *
     * @return $this
     */
    public function highlight($html, $query)
    {
        $query = $this->escapeSpecialChars($query);
        $replacement = [];
        $pattern = [];

        foreach ($this->conditions as $condition) {
            $matches = $this->getMatches($condition[0], $condition[1], $html);

            $pattern[] = $this->createPattern($condition[0], $condition[1], $matches);
            $replacement[] = $this->createReplacement($query, $matches);
        }

        $html = $this->_highlight($pattern, $replacement, $html);

        return $html;
    }

    private function getMatches($open, $close, $subject)
    {
        preg_match_all('/.' . $open . '([^<]*)' . $close . '/i', $subject, $matches);

        return $matches[2];
    }

    private function createPattern($open, $close, $search)
    {
        foreach ($search as $i => $match) {
            $match = '/.' . $open . '(' . $this->escapeSpecialChars($match) . ')' . $close . '/i';
            $search[$i] = $match;
        }

        return $search;
    }

    private function createReplacement($pattern, $subject)
    {
        $replacement = [];
        $arrPattern = explode(' ', $pattern);
        $replace = '${1}<span class="search-result-highlight">${2}</span>${3}';
        foreach ($arrPattern as $pattern) {
            $pattern = '/(.*)(' . $pattern . ')(?![^<>]*[>])(.*)/iU';
            $replacement = preg_replace($pattern, $replace, $subject);
            $subject = $replacement;
        }

        return $replacement;
    }

    private function _highlight($pattern, $replacement, $html)
    {
        foreach ($replacement as $ind => $match) {
            foreach ($match as $i => $el) {
                $el = '${1}' . $el . '${3}';
                $match[$i] = $el;
            }
            $replacement[$ind] = $match;
        }

        foreach ($pattern as $i => $search) {
            $html = preg_replace($search, $replacement[$i], $html);
        }

        return $html;
    }

    /**
     * Escape special chars in regex.
     *
     * @param string $chars
     *
     * @return string $chars
     */
    public function escapeSpecialChars($chars)
    {
        $search = ['\\', '/', '^', '[', ']', '-', ')', '(', '.', '?', '+', '*'];
        $replace = ['\\\\', '\/', '\^', '\[', '\]', '\-', '\)', '\(', '\.', '\?', '\+', '\*'];

        return str_replace($search, $replace, $chars);
    }
}
