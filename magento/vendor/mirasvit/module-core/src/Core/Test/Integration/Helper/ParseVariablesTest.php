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

class ParseVariablesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Core\Helper\ParseVariables
     */
    protected $helper;


    /**
     * @covers \Mirasvit\Core\Helper\ParseVariables::parse
     */
    public function testParse()
    {
        $this->helper = Bootstrap::getObjectManager()->create('Mirasvit\Core\Helper\ParseVariables');

        $product = new DataObject([
            'name'  => 'magento',
            'model' => 'm2'
        ]);

        $category = new DataObject([
            'name' => 'category'
        ]);

        $result = $this->helper->parse(
            '[category_name] - [product_name][ -- model: {product_model}][ -- color: {product_color}]',
            ['product' => $product, 'category' => $category]
        );

        $this->assertEquals('category - magento -- model: m2', $result);
    }
}
