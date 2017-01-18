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


namespace Mirasvit\SearchAutocomplete\Model\Index;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

abstract class AbstractIndex
{
    /**
     * @var AbstractCollection
     */
    protected $collection;

    /**
     * Set collection
     *
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Collection size
     *
     * @return int
     */
    abstract public function getSize();

    /**
     * List of items
     *
     * @return array
     */
    abstract public function getItems();
}
