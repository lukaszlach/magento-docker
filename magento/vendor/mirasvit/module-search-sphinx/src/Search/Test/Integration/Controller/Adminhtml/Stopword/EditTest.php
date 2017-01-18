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


namespace Mirasvit\Search\Controller\Adminhtml\Stopword;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\TestFramework\Helper\Bootstrap;

class EditTest extends AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Mirasvit_Search::search_stopword';
        $this->uri = 'backend/search/stopword/edit';

        parent::setUp();
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Stopword\Edit::execute
     */
    public function test404()
    {
        $data = [
            'id' => 100001,
        ];
        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/search/stopword/edit');

        $this->assertSessionMessages(
            $this->contains('This stopword no longer exists.'),
            MessageInterface::TYPE_ERROR
        );

        $this->assertTrue($this->getResponse()->isRedirect(), 'Redirect not exists.');
    }

    /**
     * @magentoDataFixture Mirasvit/Search/_files/Controller/Stopword/stopwords.php
     * @covers Mirasvit\Search\Controller\Adminhtml\Stopword\Edit::execute
     */
    public function testSuccess()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Mirasvit\Search\Model\Stopword $stopword */
        $stopword = $objectManager->create('Mirasvit\Search\Model\Stopword')->load(2);

        $data = [
            'id' => $stopword->getId(),
        ];
        $this->getRequest()->setParams($data);

        $this->dispatch('backend/search/stopword/edit');

        $this->assertFalse($this->getResponse()->isRedirect(), 'Wrong redirect at edit page.');
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $this->assertContains(
            '<h1 class="page-title">Stopword &quot;after&quot;</h1>',
            $this->getResponse()->getBody(),
            'Edit page not contains proper title.'
        );
    }
}
