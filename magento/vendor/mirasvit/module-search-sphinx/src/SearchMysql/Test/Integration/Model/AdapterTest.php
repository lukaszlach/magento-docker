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


namespace Mirasvit\SearchMysql\Model;

use Magento\TestFramework\Helper\Bootstrap;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogSearch\Model\Advanced\Request\BuilderFactory
     */
    protected $requestBuilderFactory;

    /**
     * @var \Mirasvit\SearchMysql\Model\Adapter
     */
    protected $adapter;

    protected function setUp()
    {
        // switch to mysql engine
        $config = Bootstrap::getObjectManager()->create('Magento\Config\Model\Config\Factory')->create();
        $config->setDataByPath('search/engine/engine', 'mysql2');
        $config->save();

        $this->requestBuilderFactory = Bootstrap::getObjectManager()
            ->create('\Magento\CatalogSearch\Model\Advanced\Request\BuilderFactory');

        $this->adapter = Bootstrap::getObjectManager()
            ->create('\Mirasvit\SearchMysql\Model\Adapter');
    }

    /**
     * @covers              Mirasvit\SearchMysql\Model\Adapter::query
     * @dataProvider        randomQueryProvider
     *
     * @magentoDataFixture  Mirasvit/SearchMysql/_files/Model/indexes.php
     * @magentoAppIsolation enabled
     *
     * @param string $query
     */
    public function testQuery($query)
    {
        // Product
        $queryRequest = $this->requestBuilderFactory->create()->bind('search_term', $query)
            ->setRequestName('catalogsearch_fulltext')
            ->bindDimension('scope', 1)
            ->create();

        $response = $this->adapter->query($queryRequest);
        $this->assertGreaterThanOrEqual(0, $response->count());

        // Catalog category
        $queryRequest = $this->requestBuilderFactory->create()->bind('search_term', $query)
            ->setRequestName('magento_catalog_category')
            ->bindDimension('scope', 1)
            ->create();

        $response = $this->adapter->query($queryRequest);
        $this->assertGreaterThanOrEqual(0, $response->count());

        // Cms page
        $queryRequest = $this->requestBuilderFactory->create()->bind('search_term', $query)
            ->setRequestName('magento_cms_page')
            ->bindDimension('scope', 1)
            ->create();

        $response = $this->adapter->query($queryRequest);
        $this->assertGreaterThanOrEqual(0, $response->count());
    }

    /**
     * @return array
     */
    public function randomQueryProvider()
    {
        return [
            ['query'],
            [' '],
            ['af*\"'],
            ["query''aaa, fa7940123p"],
            [''],
            ['023)($#()_*#_@|}|||\\\\//?|?|/\/~!@#$%^&*()_+']
        ];
    }
}
