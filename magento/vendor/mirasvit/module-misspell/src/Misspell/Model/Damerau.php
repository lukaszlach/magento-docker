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

use Mirasvit\Misspell\Helper\Text;

/**
 * Implementation of Damerau-Levenshtein distance algorithm
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 *  @SuppressWarnings(PHPMD.NPathComplexity) 
 */
class Damerau
{
    /**
     * @var Text
     */
    protected $text;

    /**
     * Constructor
     *
     * @param Text $textHelper
     */
    public function __construct(
        Text $textHelper
    ) {
        $this->text = $textHelper;
    }

    /**
     * Measures the Damerau-Levenshtein distance of two words
     *
     * @param string $strA
     * @param string $strB
     * @return int distance
     */
    protected function _distance($strA, $strB)
    {
        $d = [];

        $lenA = $this->text->strlen($strA);
        $lenB = $this->text->strlen($strB);

        if ($lenA == 0) {
            return $lenB;
        }

        if ($lenB == 0) {
            return $lenA;
        }

        for ($i = 0; $i <= $lenA; $i++) {
            $d[$i] = [];
            $d[$i][0] = $i;
        }

        for ($j = 0; $j <= $lenB; $j++) {
            $d[0][$j] = $j;
        }

        for ($i = 1; $i <= $lenA; $i++) {
            for ($j = 1; $j <= $lenB; $j++) {
                $cost = substr($strA, $i - 1, 1) == substr($strB, $j - 1, 1) ? 0 : 1;

                $d[$i][$j] = min(
                    $d[$i - 1][$j] + 1, // deletion
                    $d[$i][$j - 1] + 1, // insertion
                    $d[$i - 1][$j - 1] + $cost // substitution
                );

                if ($i > 1 &&
                    $j > 1 &&
                    substr($strA, $i - 1, 1) == substr($strB, $j - 2, 1) &&
                    substr($strA, $i - 2, 1) == substr($strB, $j - 1, 1)
                ) {
                    $d[$i][$j] = min(
                        $d[$i][$j],
                        $d[$i - 2][$j - 2] + $cost // transposition
                    );
                }
            }
        }

        return $d[$lenA][$lenB];
    }

    /**
     * Case insensitive version of distance()
     *
     * @param string $strA
     * @param string $strB
     * @return int distance
     */
    public function distance($strA, $strB)
    {
        return $this->_distance($this->text->strtolower($strA), $this->text->strtolower($strB));
    }

    /**
     * An attempt to measure word similarity in percent
     *
     * @param string $strA
     * @param string $strB
     *
     * @return int distance from 0 to 100
     */
    protected function _similarity($strA, $strB)
    {
        $lenA = $this->text->strlen($strA);
        $lenB = $this->text->strlen($strB);

        if ($lenA == 0 && $lenB == 0) {
            return 100;
        }

        $distance = $this->_distance($strA, $strB);
        $similarity = 100 - (int)round(200 * $distance / ($lenA + $lenB));

        return $similarity >= 100 ? 100 : $similarity;
    }

    /**
     * Case insensitive version of similarity()
     *
     * @param string $strA
     * @param string $strB
     *
     * @return int distance from 0 to 100
     */
    public function similarity($strA, $strB)
    {
        return $this->_similarity($this->text->strtolower($strA), $this->text->strtolower($strB));
    }
}
