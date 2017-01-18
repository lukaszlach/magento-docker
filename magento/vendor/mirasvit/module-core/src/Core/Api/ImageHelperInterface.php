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

interface ImageHelperInterface
{
    /**
     * Initialization
     *
     * @param DataObject $item
     * @param string     $attributeName filename attribute (image, thumbnail)
     * @param string     $imageFolder   folder with images (kb/article)
     * @param string     $imageFile     full path to file
     * @return $this
     */
    public function init(DataObject $item, $attributeName, $imageFolder = null, $imageFile = null);

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be
     * calculated.
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resize($width, $height = null);

    /**
     * Crop image
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function crop($width, $height);

    /**
     * Set image quality, values in percentage from 0 to 100.
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality);

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default.
     *
     * @param bool $flag
     * @return $this
     */
    public function constrainOnly($flag);

    /**
     * Full url to new image
     *
     * @return string
     */
    public function __toString();
}