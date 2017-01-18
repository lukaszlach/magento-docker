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



namespace Mirasvit\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Mirasvit\Core\Api\TextHelperInterface;

class Text extends AbstractHelper implements TextHelperInterface
{
    /**
     * Generate random string (all possible chars)
     *
     * @param int $length
     * @return string
     */
    public function generateRandHeavy($length)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()_+|?><~';

        return $this->generateRand($length, $characters);
    }

    /**
     * Generate random string (only numbers)
     *
     * @param int $length
     * @return string
     */
    public function generateRandNum($length)
    {
        $characters = '0123456789';

        return $this->generateRand($length, $characters);
    }

    /**
     * Generate random string (only uppercase letters)
     *
     * @param int $length
     * @return string
     */
    public function generateRandString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return $this->generateRand($length, $characters);
    }

    /**
     * Generate random string
     *
     * @param int   $length     length
     * @param array $characters allowed chars
     * @return string
     */
    public function generateRand($length, $characters)
    {
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * Split string to words
     *
     * @param string     $str                 input string
     * @param bool|false $uniqueOnly          return only unique words
     * @param int        $maxWordLength       maximum allow word length
     * @param string     $wordSeparatorRegexp space by default
     * @return array array of words
     */
    public function splitWords($str, $uniqueOnly = false, $maxWordLength = 0, $wordSeparatorRegexp = '\s')
    {
        $result = [];
        $split = preg_split('#' . $wordSeparatorRegexp . '#siu', $str, null, PREG_SPLIT_NO_EMPTY);
        foreach ($split as $word) {
            if ($uniqueOnly) {
                $result[$word] = $word;
            } else {
                $result[] = $word;
            }
        }
        if ($maxWordLength && count($result) > $maxWordLength) {
            $result = array_slice($result, 0, $maxWordLength);
        }

        return $result;
    }

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
    public function truncate($string, $length = 80, $etc = '...', &$remainder = '', $breakWords = false)
    {
        $remainder = '';
        if (0 == $length) {
            return '';
        }

        $originalLength = strlen($string);

        if ($originalLength > $length) {
            $length -= strlen($etc);
            if ($length <= 0) {
                return '';
            }
            $preparedString = $string;
            $preparedLength = $length;
            if (!$breakWords) {
                $preparedString = preg_replace('/\s+?(\S+)?$/u', '', substr($string, 0, $length + 1));
                $preparedLength = strlen($preparedString);
            }
            $remainder = substr($string, $preparedLength, $originalLength);
            return substr($preparedString, 0, $length) . $etc;
        }

        return $string;
    }
}
