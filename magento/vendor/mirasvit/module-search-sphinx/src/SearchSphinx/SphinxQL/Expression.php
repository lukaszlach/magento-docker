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



namespace Mirasvit\SearchSphinx\SphinxQL;

/**
 * Wraps expressions so they aren't quoted or modified
 * when inserted into the query
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreFile
 */
class Expression
{
    /**
     * The expression content
     *
     * @var string
     */
    protected $string;

    /**
     * The constructor accepts the expression as string
     *
     * @param string $string The content to prevent being quoted
     */
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    /**
     * Return the unmodified expression
     *
     * @return string The unaltered content of the expression
     */
    public function value()
    {
        return (string)$this->string;
    }

    /**
     * Returns the unmodified expression
     *
     * @return string The unaltered content of the expression
     */
    public function __toString()
    {
        return (string)$this->value();
    }
}
