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

class UrlRewriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Core\Helper\UrlRewrite
     */
    protected $helper;


    /**
     * @covers \Mirasvit\Core\Helper\UrlRewrite::setRewriteMode
     */
    public function testSetRewriteMode()
    {
        $this->helper = Bootstrap::getObjectManager()->create('Mirasvit\Core\Helper\UrlRewrite');
        $this->helper->setRewriteMode('core', true);

        $this->assertTrue($this->helper->isEnabled('core'));
    }

    /**
     * @covers \Mirasvit\Core\Helper\UrlRewrite::registerBasePath
     * @covers \Mirasvit\Core\Helper\UrlRewrite::registerPath
     * @covers \Mirasvit\Core\Helper\UrlRewrite::updateUrlRewrite
     * @covers \Mirasvit\Core\Helper\UrlRewrite::getUrl
     */
    public function testGetUrl()
    {
        $this->helper = Bootstrap::getObjectManager()->create('Mirasvit\Core\Helper\UrlRewrite');
        $this->helper->registerBasePath('core', 'core-module')
            ->registerPath('core', 'page', '[page-key]/[page-key2]', 'core_action');

        $entity = new DataObject([
            ['id' => 2]
        ]);

        $this->helper->updateUrlRewrite('core', 'page', $entity, ['page-key' => 'key1', 'page-key2' => 'key2']);

        $url = $this->helper->getUrl('core', 'page', $entity);

        $this->assertEquals('http://localhost/index.php/core-module/key1/key2.html', $url);
    }
}
