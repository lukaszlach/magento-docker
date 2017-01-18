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

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Core\Helper\Image
     */
    protected $helper;


    /**
     * @magentoDataFixture Mirasvit/Core/_files/Helper/image.php
     * @covers             \Mirasvit\Core\Helper\Image::__toString
     */
    public function testToString()
    {
        $this->helper = Bootstrap::getObjectManager()->create('Mirasvit\Core\Helper\Image');

        $object = new DataObject([
            'image' => 'image1.jpg'
        ]);

        $url = $this->helper->init($object, 'image', 'kb/article')
            ->resize(100, 100)
            ->__toString();

        $this->assertContains('article/cache/100x100', $url);
        $this->assertNotContains('placeholder', $url);
    }
}
