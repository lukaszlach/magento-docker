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


namespace Mirasvit\Search\Model;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDbIsolation disabled
 * @magentoDataFixture Mirasvit/Search/_files/Model/indexes.php
 */
class IndexTest extends \PHPUnit_Framework_TestCase
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
        $this->productIndex = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('catalogsearch_fulltext');

        $this->categoryIndex = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('magento_catalog_category');

        $this->pageIndex = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('magento_cms_page');
    }

    public function testGetIndexInstance()
    {
        $this->assertInstanceOf(
            'Mirasvit\Search\Model\Index\Magento\Catalog\Product\Index',
            $this->productIndex->getIndexInstance()
        );

        $this->assertInstanceOf(
            'Mirasvit\Search\Model\Index\Magento\Catalog\Category\Index',
            $this->categoryIndex->getIndexInstance()
        );

        $this->assertInstanceOf(
            'Mirasvit\Search\Model\Index\Magento\Cms\Page\Index',
            $this->pageIndex->getIndexInstance()
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testGetIndexInstanceException()
    {
        /** @var \Mirasvit\Search\Model\Index $index */
        $index = Bootstrap::getObjectManager()->create('\Mirasvit\Search\Model\Index')
            ->load('fake_index');

        $index->getIndexInstance();
    }
}
