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

class Pool
{
    /**
     * @var AbstractIndex[]
     */
    protected $pool;

    /**
     * Constructor
     *
     * @param [] $indexes
     */
    public function __construct(
        $indexes = []
    ) {
        $this->pool = $indexes;
    }

    /**
     * Return index for object, based on object code
     *
     * @param string $code
     *
     * @return AbstractIndex|false
     */
    public function get($code)
    {
        foreach ($this->pool as $index) {
            if ($code == $index['code']) {
                return $index['class'];
            }
        }

        return false;
    }
}
