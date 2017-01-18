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


namespace Mirasvit\Core\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\Factory as FrameworkImageFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Image
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UrlInterface
     */
    protected $urlManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var FrameworkImageFactory
     */
    protected $imageFactory;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var string
     */
    protected $baseMediaPath;

    /**
     * @var string
     */
    protected $baseMediaUrl;

    /**
     * @var string
     */
    protected $subDir;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $quality = 90;

    /**
     * @var bool
     */
    protected $keepFrame = true;

    /**
     * @var bool
     */
    protected $keepTransparency = true;

    /**
     * @var bool
     */
    protected $constrainOnly = true;

    /**
     * @var array
     */
    protected $backgroundColor = [255, 255, 255];

    /**
     * @var string
     */
    protected $baseFile;

    /**
     * @var bool
     */
    protected $isBaseFilePlaceholder;

    /**
     * @var string
     */
    protected $newFile;

    /**
     * @var \Magento\Framework\Image
     */
    protected $processor;

    /**
     * @var string
     */
    protected $destinationSubDir;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AssetRepository
     */
    protected $assetRepo;

    /**
     * @param Filesystem            $filesystem
     * @param UrlInterface          $urlManager
     * @param ScopeConfigInterface  $scopeConfig
     * @param FrameworkImageFactory $imageFactory
     * @param StoreManagerInterface $storeManager
     * @param AssetRepository       $assetRepo
     */
    public function __construct(
        Filesystem $filesystem,
        UrlInterface $urlManager,
        ScopeConfigInterface $scopeConfig,
        FrameworkImageFactory $imageFactory,
        StoreManagerInterface $storeManager,
        AssetRepository $assetRepo
    ) {
        $this->filesystem = $filesystem;
        $this->urlManager = $urlManager;
        $this->scopeConfig = $scopeConfig;
        $this->imageFactory = $imageFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->baseMediaPath = rtrim($this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(), '/');
        $this->baseMediaUrl = rtrim($storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/');
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Width
     *
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Height
     *
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Quality
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Quality
     *
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default.
     *
     * @param bool $flag
     * @return $this
     */
    public function setConstrainOnly($flag)
    {
        $this->constrainOnly = (bool)$flag;

        return $this;
    }

    /**
     * Check memory
     *
     * @param string|null $file
     * @return bool
     */
    protected function checkMemory($file = null)
    {
        return $this->getMemoryLimit() >
        ($this->getMemoryUsage() + $this->getNeedMemoryForFile($file)) || $this->getMemoryLimit() == -1;
    }

    /**
     * Server memory limit
     *
     * @return int
     */
    protected function getMemoryLimit()
    {
        $memoryLimit = trim(strtoupper(ini_get('memory_limit')));

        if (!isset($memoryLimit[0])) {
            $memoryLimit = '128M';
        }

        if (substr($memoryLimit, -1) == 'K') {
            return substr($memoryLimit, 0, -1) * 1024;
        }
        if (substr($memoryLimit, -1) == 'M') {
            return substr($memoryLimit, 0, -1) * 1024 * 1024;
        }
        if (substr($memoryLimit, -1) == 'G') {
            return substr($memoryLimit, 0, -1) * 1024 * 1024 * 1024;
        }

        return $memoryLimit;
    }

    /**
     * Current memory usage
     *
     * @return int
     */
    protected function getMemoryUsage()
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage();
        }

        return 0;
    }

    /**
     * Memory that need for resize image
     *
     * @param null|string $file
     * @return int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getNeedMemoryForFile($file = null)
    {
        $file = $file ? $file : $this->getBaseFile();

        if (!file_exists($file) || !is_file($file)) {
            return 0;
        }

        try {
            $imageInfo = getimagesize($file);
        } catch (\Exception $e) {
            return 0;
        }

        if (!isset($imageInfo[0]) || !isset($imageInfo[1])) {
            return 0;
        }

        // if there is no info about this parameter lets set it for maximum
        if (!isset($imageInfo['channels'])) {
            $imageInfo['channels'] = 4;
        }

        // if there is no info about this parameter lets set it for maximum
        if (!isset($imageInfo['bits'])) {
            $imageInfo['bits'] = 8;
        }

        return round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 +
                pow(2, 16)) * 1.65);
    }

    /**
     * Convert array of 3 items (decimal r, g, b) to string of their hex values.
     *
     * @param array $rgbArray
     * @return string
     */
    protected function rgbToString($rgbArray)
    {
        $result = [];
        foreach ($rgbArray as $value) {
            if (null === $value) {
                $result[] = 'null';
            } else {
                $result[] = sprintf('%02s', dechex($value));
            }
        }

        return implode($result);
    }

    /**
     * Set image sub directory
     *
     * @param string $dir
     * @return $this
     */
    public function setSubDir($dir)
    {
        $this->subDir = $dir;

        return $this;
    }

    /**
     * Image sub directory
     *
     * @return string
     */
    public function getSubDir()
    {
        return $this->subDir;
    }

    /**
     * Set filename for base file and new file.
     *
     * @param string $file
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setBaseFile($file)
    {
        $this->isBaseFilePlaceholder = false;

        if ($file && $this->getSubDir()) {
            $file = $this->getSubDir() . '/' . $file;
        }

        $baseDir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath();

        if ('/no_selection' == $file) {
            $file = null;
        }

        if ($file) {
            if (!file_exists($baseDir . $file) || !$this->checkMemory($baseDir . $file)) {
                $file = null;
            }
        }

        if (!$file) {
            $this->isBaseFilePlaceholder = true;
            // check if placeholder defined in config
            $isConfigPlaceholder = $this->scopeConfig->getValue(
                "catalog/placeholder/{$this->getDestinationSubDir()}_placeholder",
                ScopeInterface::SCOPE_STORE
            );
            $configPlaceholder = '/catalog/product/placeholder/' . $isConfigPlaceholder;

            if (!empty($isConfigPlaceholder) && $this->fileExists($baseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            } else {
                $this->newFile = true;

                return $this;
            }

        }

        $baseFile = $baseDir . $file;

        if (!$file || !file_exists($baseFile)) {
            return $this;
        }

        $this->baseFile = $baseFile;

        // build new filename (most important params)

        $path = [
            $this->baseMediaPath,
            $this->getSubDir(),
            'cache'
        ];

        if ($this->width || $this->height) {
            $path[] = "{$this->width}x{$this->height}";
        }

        // add misc params as a hash
        $miscParams = [
            ($this->keepFrame ? '' : 'no') . 'frame',
            ($this->keepTransparency ? '' : 'no') . 'transparency',
            ($this->constrainOnly ? 'do' : 'not') . 'constrainonly',
            $this->rgbToString($this->backgroundColor),
            'quality' . $this->quality
        ];

        $path[] = md5(implode('_', $miscParams));
        $pathInfo = pathinfo($file);
        $path[] = md5($file) . '.' . $pathInfo['extension'];

        // append prepared filename
        $this->newFile = implode('/', $path); // the $file contains heading slash

        return $this;
    }

    /**
     * Base file
     *
     * @return string
     */
    public function getBaseFile()
    {
        return $this->baseFile;
    }

    /**
     * New file
     *
     * @return string
     */
    public function getNewFile()
    {
        return $this->newFile;
    }

    /**
     * Image Processor
     *
     * @param \Magento\Framework\Image $processor
     * @return $this
     */
    public function setImageProcessor($processor)
    {
        $this->processor = $processor;

        return $this;
    }

    /**
     * Image Processor
     *
     * @return \Magento\Framework\Image
     */
    public function getImageProcessor()
    {
        if (!$this->processor) {
            $filename = $this->getBaseFile();
            $this->processor = $this->imageFactory->create($filename);
        }
        $this->processor->keepAspectRatio(true);
        $this->processor->keepFrame($this->keepFrame);
        $this->processor->keepTransparency($this->keepTransparency);
        $this->processor->constrainOnly($this->constrainOnly);
        $this->processor->backgroundColor($this->backgroundColor);
        $this->processor->quality($this->quality);

        return $this->processor;
    }

    /**
     * Reseize image
     *
     * @return $this
     */
    public function resize()
    {
        if ($this->newFile === true) {
            return $this;
        }

        if (!$this->getWidth() && !$this->getHeight()) {
            return $this;
        }

        # if height not specified, we calculate it manually
        if ($this->height == null) {
            $ratio = $this->width / $this->getImageProcessor()->getOriginalWidth();
            if ($ratio > 1) {
                $this->height = $this->getImageProcessor()->getOriginalHeight();
            } else {
                $this->height = $this->getImageProcessor()->getOriginalHeight() * $ratio;
            }
        }

        $this->getImageProcessor()->resize($this->width, $this->height);

        return $this;
    }

    /**
     * Crop image
     *
     * @return $this
     */
    public function crop()
    {
        if ($this->newFile === true) {
            return $this;
        }

        if (!$this->getWidth() && !$this->getHeight()) {
            return $this;
        }

        $this->keepFrame = false;
        $w = $nw = $this->getImageProcessor()->getOriginalWidth();
        $h = $nh = $this->getImageProcessor()->getOriginalHeight();

        $left = $top = 0;

        $scaleW = $this->width / $nw;

        $scaleH = $this->height / $nh;

        $scale = max($scaleW, $scaleH);
        $nw = $w * $scale;
        $nh = $h * $scale;
        if ($nw > $this->width) {
            $left = $right = ($nw - $this->width) / 2;
        }
        if ($nh > $this->height) {
            $top = $bottom = ($nh - $this->height) / 2;
        }
        $left = $right = intval($left);
        $top = $bottom = intval($top);

        $this->getImageProcessor()->resize($nw, $nh);
        $this->getImageProcessor()->crop($top, $left, $right, $bottom);

        return $this;
    }

    /**
     * Save processed file
     *
     * @return $this
     */
    public function saveFile()
    {
        if ($this->newFile !== true) {
            $this->getImageProcessor()->save($this->getNewFile());
        }

        return $this;
    }

    /**
     * Absolute url to file
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->newFile === true) {
            $url = $this->assetRepo->getUrl(
                "Magento_Catalog::images/product/placeholder/{$this->getDestinationSubdir()}.jpg"
            );
        } else {
            $baseDir = $this->baseMediaPath;
            $path = str_replace($baseDir, '', $this->newFile);

            $url = $this->baseMediaUrl . $path;
        }

        return $url;
    }

    /**
     * Destination sub directory
     *
     * @param string $dir
     * @return $this
     */
    public function setDestinationSubDir($dir)
    {
        $this->destinationSubDir = $dir;

        return $this;
    }

    /**
     * Destination sub directory
     *
     * @return string
     */
    public function getDestinationSubDir()
    {
        return $this->destinationSubDir;
    }

    /**
     * Is image already exists?
     *
     * @return bool
     */
    public function isCached()
    {
        return file_exists($this->newFile);
    }

    /**
     * Is placeholder
     *
     * @return bool
     */
    public function isFilePlaceholder()
    {
        return $this->isBaseFilePlaceholder;
    }

    /**
     * Is file exists?
     *
     * @param string $filename
     * @return bool
     */
    protected function fileExists($filename)
    {
        return file_exists($filename);
    }
}
