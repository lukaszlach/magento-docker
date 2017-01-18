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


/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mirasvit\SearchSphinx\Model\Adapter\Field;

class Field implements FieldInterface
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var int
     */
    private $attributeId;

    /**
     * @var int
     */
    private $type;

    /**
     * @param string $column
     * @param int|null $attributeId
     * @param int $type
     */
    public function __construct($column, $attributeId = null, $type = self::TYPE_FULLTEXT)
    {
        $this->column = $column;
        $this->attributeId = $attributeId;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return int|null
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
}
