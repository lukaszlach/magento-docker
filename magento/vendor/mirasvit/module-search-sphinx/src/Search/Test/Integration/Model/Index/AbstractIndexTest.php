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
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.34
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Model\Index;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDataFixture Mirasvit/Search/_files/Model/indexes.php
 */
class AbstractIndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Search\Model\Index;
     */
    protected $productIndex;

    /**
     * @var \Mirasvit\Search\Model\Index;
     */
    protected $categoryIndex;

    /**
     * @var \Mirasvit\Search\Model\Index;
     */
    protected $pageIndex;


    protected function setUp()
    {
        // switch to mysql engine
        $config = Bootstrap::getObjectManager()->create('Magento\Config\Model\Config\Factory')->create();
        $config->setDataByPath('search/engine/engine', 'mysql');
        $config->save();

        $this->productIndex = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('catalogsearch_fulltext');

        $this->categoryIndex = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('magento_catalog_category');

        $this->pageIndex = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('magento_cms_page');
    }

    /**
     * @covers Mirasvit\Search\Model\Index\AbstractIndex::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Product', $this->productIndex->getIndexInstance()->getName());
        $this->assertEquals('Category', $this->categoryIndex->getIndexInstance()->getName());
        $this->assertEquals('Cms Page', $this->pageIndex->getIndexInstance()->getName());
    }

    /**
     * @covers Mirasvit\Search\Model\Index\AbstractIndex::toString
     */
    public function testToString()
    {
        $this->assertEquals('Magento / Product', $this->productIndex->getIndexInstance()->toString());
        $this->assertEquals('Magento / Category', $this->categoryIndex->getIndexInstance()->toString());
        $this->assertEquals('Magento / Cms Page', $this->pageIndex->getIndexInstance()->toString());
    }

    /**
     * @covers Mirasvit\Search\Model\Index\AbstractIndex::reindexAll
     */
    public function testReindexAll()
    {
        $this->assertTrue($this->productIndex->getIndexInstance()->reindexAll());
        $this->assertTrue($this->categoryIndex->getIndexInstance()->reindexAll());
        $this->assertTrue($this->pageIndex->getIndexInstance()->reindexAll());

        $this->productIndex->load($this->productIndex->getId());
        $this->categoryIndex->load($this->categoryIndex->getId());
        $this->pageIndex->load($this->pageIndex->getId());

        $this->assertEquals(1, $this->productIndex->getStatus());
        $this->assertEquals(1, $this->categoryIndex->getStatus());
        $this->assertEquals(1, $this->pageIndex->getStatus());
    }

    /**
     * @covers Mirasvit\Search\Model\Index\AbstractIndex::getSearchableEntities
     */
    public function testGetSearchableEntities()
    {
        $this->productIndex->getIndexInstance()->getSearchableEntities(1);
        $this->assertGreaterThan(0, $this->categoryIndex->getIndexInstance()->getSearchableEntities(1)->getSize());
        $this->assertGreaterThan(0, $this->pageIndex->getIndexInstance()->getSearchableEntities(1)->getSize());
    }

    /**
     * @covers Mirasvit\Search\Model\Index\AbstractIndex::getSearchCollection
     */
    public function testGetSearchCollection()
    {
        $_GET['q'] = 'test';
        $this->productIndex->getIndexInstance()->getSearchCollection();
        $this->categoryIndex->getIndexInstance()->getSearchCollection();
        $this->pageIndex->getIndexInstance()->getSearchCollection();
    }
}
