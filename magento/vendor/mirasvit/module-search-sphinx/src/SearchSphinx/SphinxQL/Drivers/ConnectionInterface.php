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



namespace Mirasvit\SearchSphinx\SphinxQL\Drivers;

use Mirasvit\SearchSphinx\SphinxQL\Exception\DatabaseException;
use Mirasvit\SearchSphinx\SphinxQL\Exception\SphinxQLException;
use Mirasvit\SearchSphinx\SphinxQL\Expression;

/**
 * Interface ConnectionInterface
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreFile
 */
interface ConnectionInterface
{
    /**
     * Performs a query on the Sphinx server.
     *
     * @param string $query The query string
     *
     * @return ResultSetInterface The result array or number of rows affected
     * @throws DatabaseException If the executed query produced an error
     */
    public function query($query);

    /**
     * Performs multiple queries on the Sphinx server.
     *
     * @param array $queue Queue holding all of the queries to be executed
     *
     * @return MultiResultSetInterface The result array
     * @throws DatabaseException In case a query throws an error
     * @throws SphinxQLException In case the array passed is empty
     */
    public function multiQuery(Array $queue);

    /**
     * Escapes the input
     *
     * @param string $value The string to escape
     *
     * @return string The escaped string
     * @throws DatabaseException If an error was encountered during server-side escape
     */
    public function escape($value);

    /**
     * Wraps the input with identifiers when necessary.
     *
     * @param Expression|string $value The string to be quoted, or an Expression to leave it untouched
     *
     * @return Expression|string The untouched Expression or the quoted string
     */
    public function quoteIdentifier($value);

    /**
     * Calls $this->quoteIdentifier() on every element of the array passed.
     *
     * @param array $array An array of strings to be quoted
     *
     * @return array The array of quoted strings
     */
    public function quoteIdentifierArr(Array $array = array());

    /**
     * Adds quotes around values when necessary.
     *
     * @param Expression|string $value The input string, eventually wrapped in an expression to leave it untouched
     *
     * @return Expression|string|int The untouched Expression or the quoted string
     */
    public function quote($value);

    /**
     * Calls $this->quote() on every element of the array passed.
     *
     * @param array $array The array of strings to quote
     *
     * @return array The array of quotes strings
     */
    public function quoteArr(Array $array = array());
}
