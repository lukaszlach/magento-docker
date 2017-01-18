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

/**
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreFile
 */
interface MultiResultSetInterface extends \ArrayAccess, \Iterator, \Countable
{
    /**
     * Stores all the data in PHP and frees the data on the server
     *
     * @return static
     */
    public function store();

    /**
     * Returns the stored data as an array (results) of arrays (rows)
     *
     * @return ResultSetInterface[]
     */
    public function getStored();

    /**
     * Returns the next result set, or false if there's no more results
     *
     * @return ResultSetInterface|false
     */
    public function getNext();
}
