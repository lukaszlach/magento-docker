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


namespace Mirasvit\Misspell\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDbIsolation disabled
 * @magentoDataFixture Mirasvit/Misspell/_files/products.php
 */
class MisspellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Misspell\Model\Indexer
     */
    protected $indexer;

    /**
     * @var \Mirasvit\Misspell\Model\Misspell
     */
    protected $misspell;

    protected function setUp()
    {
        $this->indexer = Bootstrap::getObjectManager()->create('\Mirasvit\Misspell\Model\Indexer');
        $this->misspell = Bootstrap::getObjectManager()->create('\Mirasvit\Misspell\Model\Misspell');
    }

    public function testMisspell()
    {
        $this->indexer->executeFull();

        $this->assertEquals('Samsung Gallaxy S4', $this->misspell->getSuggest('Samsing Galaxy S4'));
        $this->assertEquals('Nokia', $this->misspell->getSuggest('Nokia'));
        $this->assertEquals('ABC-1234', $this->misspell->getSuggest('ABC-1214'));
    }
}
