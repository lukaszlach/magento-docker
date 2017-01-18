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


namespace Mirasvit\Misspell\Controller;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class ResultTest extends AbstractController
{
    /**
     * @magentoDataFixture Mirasvit/Misspell/_files/products.php
     */
    public function testIndexActionMisspell()
    {
        Bootstrap::getObjectManager()->create('\Mirasvit\Misspell\Model\Indexer')->executeFull();

        $this->getRequest()->setParams(['q' => 'samsing']);
        $this->dispatch('catalogsearch/result');

        $expect = new \PHPUnit_Framework_Constraint_IsEqual(
            'http://localhost/index.php/catalogsearch/result/?o=samsing&q=samsung'
        );

        $this->assertRedirect($expect);
    }

    /**
     * @magentoDataFixture Mirasvit/Misspell/_files/products.php
     */
    public function testIndexActionFallback()
    {
        Bootstrap::getObjectManager()->create('\Mirasvit\Misspell\Model\Indexer')->executeFull();

        $this->getRequest()->setParams(['q' => 'samsing vafafdsafasdf']);
        $this->dispatch('catalogsearch/result');

        $expect = new \PHPUnit_Framework_Constraint_IsEqual(
            'http://localhost/index.php/catalogsearch/result/?f=samsing+vafafdsafasdf&q=vafafdsafasdf'
        );

        $this->assertRedirect($expect);
    }

    /**
     * @magentoDataFixture Mirasvit/Misspell/_files/products.php
     */
    public function testIndexActionWithResults()
    {
        Bootstrap::getObjectManager()->create('\Mirasvit\Misspell\Model\Indexer')->executeFull();

        $this->getRequest()->setParams(['q' => 'samsung']);
        $this->dispatch('catalogsearch/result');

        $this->assertFalse($this->getResponse()->isRedirect());
    }
}
