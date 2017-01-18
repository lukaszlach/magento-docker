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


namespace Mirasvit\Search\Model\Index;

class Pool
{
    /**
     * List of registered indexes
     *
     * @var AbstractIndex[]
     */
    protected $pool;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param [] $indexes
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $indexes = []
    ) {
        $this->pool = $indexes;
        $this->objectManager = $objectManager;
    }


    /**
     * List of available search indexes
     *
     * @return AbstractIndex[]
     */
    public function getAvailableIndexes()
    {
        $result = [];

        foreach ($this->pool as $index) {
            $result[] = $this->objectManager->get($index);
        }

        return $result;
    }

    /**
     * Search index by index code
     *
     * @param string $code
     * @return bool|AbstractIndex
     */
    public function get($code)
    {
        foreach ($this->pool as $index) {
            if ($this->objectManager->get($index)->getCode() == $code) {
                return $this->objectManager->create($index);
            }
        }

        return false;
    }
}
