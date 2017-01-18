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
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Api;

use Magento\Framework\DataObject;

interface TextHelperInterface
{
    /**
     * Generate random string (all possible chars)
     *
     * @param int $length
     * @return string
     */
    public function generateRandHeavy($length);

    /**
     * Generate random string (only numbers)
     *
     * @param int $length
     * @return string
     */
    public function generateRandNum($length);

    /**
     * Generate random string (only uppercase letters)
     *
     * @param int $length
     * @return string
     */
    public function generateRandString($length);

    /**
     * Generate random string
     *
     * @param int   $length     length
     * @param array $characters allowed chars
     * @return string
     */
    public function generateRand($length, $characters);

    /**
     * Split string to words
     *
     * @param string     $str                 input string
     * @param bool|false $uniqueOnly          return only unique words
     * @param int        $maxWordLength       maximum allow word length
     * @param string     $wordSeparatorRegexp space by default
     * @return array array of words
     */
    public function splitWords($str, $uniqueOnly = false, $maxWordLength = 0, $wordSeparatorRegexp = '\s');

    /**
     * Truncate a string to a certain length if necessary, appending the $etc string.
     * $remainder will contain the string that has been replaced with $etc.
     *
     * @param string $string
     * @param int    $length
     * @param string $etc
     * @param string &$remainder
     * @param bool   $breakWords
     * @return string
     */
    public function truncate($string, $length = 80, $etc = '...', &$remainder = '', $breakWords = false);
}