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
 * @package   mirasvit/module-misspell
 * @version   1.0.7
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Misspell\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Misspell\Helper\Text
     */
    protected $model;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->model = $this->objectManager->getObject('\Mirasvit\Misspell\Helper\Text', []);
    }

    /**
     * @param null|string $expected
     * @param string      $data
     * @dataProvider prepareCleanStringProvider
     */
    public function testCleanString($expected, $data)
    {
        $this->assertEquals($expected, $this->model->cleanString($data));
    }

    /**
     * @param null|string $expected
     * @param string      $data
     * @dataProvider prepareSplitWordsProvider
     */
    public function testSplitWords($expected, $data)
    {
        $this->assertEquals($expected, $this->model->splitWords($data));
    }

    /**
     * @param null|string $expected
     * @param string      $data
     * @dataProvider prepareGetTrigramProvider
     */
    public function testGetTrigram($expected, $data)
    {
        $this->assertEquals($expected, $this->model->getTrigram($data));
    }

    /**
     * @return array
     */
    public function prepareCleanStringProvider()
    {
        return [
            [
                'string 1',
                'string 1'
            ],
            [
                'string',
                'string%^'
            ],
            [
                'string 0-abc',
                'string &*0-abc'
            ],
            [
                'Тестовая Строка',
                'Тестовая Строка:'
            ],
        ];
    }

    /**
     * @return array
     */
    public function prepareSplitWordsProvider()
    {
        return [
            [
                ['string', '1'],
                'string 1'
            ],
            [
                ['string'],
                'string'
            ],
            [
                ['string-a', 'string', 'b'],
                'string-a string b string'
            ],
        ];
    }

    /**
     * @return array
     */
    public function prepareGetTrigramProvider()
    {
        return [
            [
                '___s __st _str stri trin ring ing_ ng__ g___',
                'string'
            ],
            [
                '___a __a- _a-b a-bc -bc_ bc__ c___',
                'a-bc'
            ],
        ];
    }
}
