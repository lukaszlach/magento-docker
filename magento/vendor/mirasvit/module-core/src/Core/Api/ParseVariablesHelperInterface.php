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

interface ParseVariablesHelperInterface
{
    /**
     * Parse string.
     * [product_name][, model: {product_model}!] [product_nonexists]  [buy it {product_nonexists} !]
     *
     * @param string   $str
     * @param array    $objects
     * @param array    $additional
     * @param bool|int $storeId
     * @return string
     */
    public function parse($str, $objects, $additional = [], $storeId = false);
}