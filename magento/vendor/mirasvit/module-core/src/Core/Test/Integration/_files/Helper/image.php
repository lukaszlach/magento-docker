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


use Magento\Framework\App\Filesystem\DirectoryList;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $mediaDirectory \Magento\Framework\Filesystem\Directory\WriteInterface */
$mediaDirectory = $objectManager->get('Magento\Framework\Filesystem')
    ->getDirectoryWrite(DirectoryList::MEDIA);

$targetDirPath = 'kb/article';
$mediaDirectory->create($targetDirPath);
$targetPath = $mediaDirectory->getAbsolutePath($targetDirPath);

$files = glob($targetPath . '/*');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

copy(__DIR__ . '/images/image1.jpg', $targetPath . '/image1.jpg');
copy(__DIR__ . '/images/image2.jpg', $targetPath . '/image2.jpg');
copy(__DIR__ . '/images/image3.jpg', $targetPath . '/image3.jpg');