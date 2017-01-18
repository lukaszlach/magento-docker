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
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Mirasvit\Core\Api\ImageHelperInterface;
use Mirasvit\Core\Model\ImageFactory;

class Image extends AbstractHelper implements ImageHelperInterface
{
    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AssetRepository
     */
    protected $assetRepo;

    /**
     * @var \Mirasvit\Core\Model\Image
     */
    protected $model;

    /**
     * @var bool
     */
    protected $scheduleResize = false;

    /**
     * @var bool
     */
    protected $scheduleCrop = false;

    /**
     * @var DataObject
     */
    protected $item;

    /**
     * @var string
     */
    protected $imageFile;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * Constructor
     *
     * @param ImageFactory    $imageFactory
     * @param Context         $context
     * @param AssetRepository $assetRepo
     */
    public function __construct(
        ImageFactory $imageFactory,
        Context $context,
        AssetRepository $assetRepo
    ) {
        $this->imageFactory = $imageFactory;
        $this->context = $context;
        $this->scopeConfig = $context->getScopeConfig();
        $this->assetRepo = $assetRepo;

        parent::__construct($context);
    }

    /**
     * Initialization
     *
     * @param DataObject $item
     * @param string     $attributeName filename attribute (image, thumbnail)
     * @param string     $imageFolder   folder with images (kb/article)
     * @param string     $imageFile     full path to file
     * @return $this
     */
    public function init(DataObject $item, $attributeName, $imageFolder = null, $imageFile = null)
    {
        $this->reset();
        $this->setModel($this->imageFactory->create());
        $this->getModel()->setDestinationSubDir($attributeName);
        $this->getModel()->setSubDir($imageFolder);
        $this->setItem($item);

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            $this->getModel()->setBaseFile(
                $this->getItem()->getData($this->getModel()->getDestinationSubDir())
            );
        }

        return $this;
    }

    /**
     * Placeholder relative path
     *
     * @return string
     */
    public function getPlaceholder()
    {
        $attr = $this->getModel()->getDestinationSubDir();
        $subDir = $this->getModel()->getSubDir();

        if ($subDir) {
            $attr = $subDir . '/' . $attr;
        }

        $this->placeholder = 'images/placeholder/' . $attr . '.jpg';

        return $this->placeholder;
    }

    /**
     * Is placeholder
     *
     * @return bool
     */
    public function isImagePlaceholder()
    {
        return $this->getModel()->isFilePlaceholder();
    }

    /**
     * Reset all previous data
     *
     * @return $this
     */
    protected function reset()
    {
        $this->model = null;
        $this->scheduleResize = false;
        $this->scheduleCrop = false;
        $this->item = null;
        $this->imageFile = null;

        return $this;
    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be
     * calculated.
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resize($width, $height = null)
    {
        $this->getModel()
            ->setWidth($width)
            ->setHeight($height);
        $this->scheduleResize = true;

        return $this;
    }

    /**
     * Crop image
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function crop($width, $height)
    {
        $this->getModel()
            ->setWidth($width)
            ->setHeight($height);

        $this->scheduleCrop = true;

        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100.
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->getModel()->setQuality($quality);

        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default.
     *
     * @param bool $flag
     * @return $this
     */
    public function constrainOnly($flag)
    {
        $this->getModel()->setConstrainOnly($flag);

        return $this;
    }

    /**
     * Full url to new image
     *
     * @return string
     */
    public function __toString()
    {
        try {
            if ($this->getImageFile()) {
                $this->getModel()->setBaseFile($this->getImageFile());
            } else {
                $this->getModel()->setBaseFile($this->getItem()->getData($this->getModel()->getDestinationSubDir()));
            }

            if ($this->getModel()->isCached()) {
                return $this->getModel()->getUrl();
            } else {
                if ($this->scheduleResize) {
                    $this->getModel()->resize();
                }

                if ($this->scheduleCrop) {
                    $this->getModel()->crop();
                }

                $url = $this->getModel()
                    ->saveFile()
                    ->getUrl();
            }
        } catch (\Exception $e) {
            $url = $this->assetRepo->getUrl($this->getPlaceholder());
        }

        return $url;
    }

    /**
     * Image Model
     *
     * @param \Mirasvit\Core\Model\Image $model
     * @return $this
     */
    protected function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Image model
     *
     * @return \Mirasvit\Core\Model\Image
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Item entity
     *
     * @param DataObject $item
     * @return $this
     */
    protected function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Item entity
     *
     * @return DataObject
     */
    protected function getItem()
    {
        return $this->item;
    }

    /**
     * Full path to image file
     *
     * @param string $file
     * @return $this
     */
    protected function setImageFile($file)
    {
        $this->imageFile = $file;

        return $this;
    }

    /**
     * Full path to image file
     *
     * @return string
     */
    protected function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Parse string NxN to width and height
     *
     * @param string $string
     * @return array|bool
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return ['width' => ($size[0] > 0) ? $size[0] : null, 'height' => ($size[1] > 0) ? $size[1] : null];
        }

        return false;
    }

    /**
     * Retrieve original image width.
     *
     * @return int null
     */
    public function getOriginalWidth()
    {
        return $this->getModel()
            ->getImageProcessor()
            ->getOriginalWidth();
    }

    /**
     * Retrieve original image height.
     *
     * @return int null
     */
    public function getOriginalHeight()
    {
        return $this->getModel()
            ->getImageProcessor()
            ->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height.
     *
     * @return array
     */
    public function getOriginalSizeArray()
    {
        return [$this->getOriginalWidth(), $this->getOriginalHeight()];
    }

    /**
     * Check - is this file an image.
     *
     * @param string $filePath
     * @return bool
     * @throws \Exception
     */
    public function validateUploadFile($filePath)
    {
        if (!getimagesize($filePath)) {
            throw new \Exception(__('Disallowed file type.'));
        }

        return true;
    }
}
