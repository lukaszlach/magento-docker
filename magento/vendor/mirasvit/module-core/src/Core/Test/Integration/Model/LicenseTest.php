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

use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;

class LicenseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Core\Model\License
     */
    protected $model;

    public function setUp()
    {
        $this->model = Bootstrap::getObjectManager()->create('Mirasvit\Core\Model\License');
    }

    /**
     * @covers \Mirasvit\Core\Model\License::getDomain
     */
    public function testGetDomain()
    {
        $domain = $this->model->getDomain();
        $this->assertEquals('http://localhost:81/', $domain);
    }

    /**
     * @covers \Mirasvit\Core\Model\License::getIP
     */
    public function testGetIP()
    {
        $this->model->getIP();
    }

    /**
     * @covers \Mirasvit\Core\Model\License::getEdition
     */
    public function testGetEdition()
    {
        $this->assertEquals('CE', $this->model->getEdition());
    }

    /**
     * @covers \Mirasvit\Core\Model\License::getVersion
     */
    public function testGetVersion()
    {
        $this->assertContains('2.', $this->model->getVersion());
    }

    /**
     * @covers \Mirasvit\Core\Model\License::setRequest
     */
    public function testSendRequest()
    {
        $response = $this->model->sendRequest('http://mirasvit.com/lc/check/', ['v' => 3]);
        $this->assertArrayHasKey('status', $response);

        $response = $this->model->sendRequest('http://mirasvit.com/lc/check/', []);
        $this->assertArrayNotHasKey('status', $response);
    }

    /**
     * @covers \Mirasvit\Core\Model\License::request
     */
    public function testRequest()
    {
        $this->model->request();
    }

    /**
     * @covers \Mirasvit\Core\Model\License::getStatus
     */
    public function testGetStatus()
    {
        $status = $this->model->getStatus();

        $this->assertEquals('active', $status);
    }
}