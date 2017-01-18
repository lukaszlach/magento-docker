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



namespace Mirasvit\Core\Test\Unit\Helper;

use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Core\Helper\Text
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Mirasvit\Core\Helper\Text|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * setup
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            []
        );
        $this->helper = $this->objectManager->getObject(
            '\Mirasvit\Core\Helper\Text',
            []
        );
    }

    /**
     * @covers \Mirasvit\Core\Helper\Text::generateRandHeavy
     */
    public function testGenerateRandHeavy()
    {
        $this->assertEquals(5, strlen($this->helper->generateRandHeavy(5)));
    }

    /**
     * @covers \Mirasvit\Core\Helper\Text::generateRandNum
     */
    public function testGenerateRandNum()
    {
        $this->assertEquals(3, strlen($this->helper->generateRandNum(3)));
    }

    /**
     * @covers \Mirasvit\Core\Helper\Text::generateRandString
     */
    public function testGenerateRandString()
    {
        $this->assertEquals(10, strlen($this->helper->generateRandString(10)));
    }

    /**
     * @covers \Mirasvit\Core\Helper\Text::generateRand
     */
    public function testGenerateRand()
    {
        $this->assertEquals('aaaa', $this->helper->generateRand(4, 'a'));
    }

    /**
     * @covers \Mirasvit\Core\Helper\Text::splitWords
     */
    public function testSplitWords()
    {
        $result = $this->helper->splitWords('magento2 core extension');

        $this->assertEquals(['magento2', 'core', 'extension'], $result);
    }

    /**
     * @covers \Mirasvit\Core\Helper\Text::truncate
     */
    public function testTruncate()
    {
        $result = $this->helper->truncate('some super long string');
        $this->assertEquals('some super long string', $result);

        $result = $this->helper->truncate('some super long string', 20);
        $this->assertEquals('some super long...', $result);
    }
}
