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


namespace Mirasvit\Search\Controller\Adminhtml\Index;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

class SaveTest extends AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Mirasvit_Search::search_index';
        $this->uri = 'backend/search/index/save';

        parent::setUp();
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Index\Save::execute
     */
    public function testExecuteSuccess()
    {
        $data = [
            'title' => 'some',
            'code'  => 'magento_catalog_category'
        ];

        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/search/index/save');

        $this->assertSessionMessages(
            $this->isEmpty(),
            MessageInterface::TYPE_ERROR
        );

        $this->assertSessionMessages(
            $this->contains('You saved the search index.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Index\Save::execute
     */
    public function testExecuteNotExists()
    {
        $data = [
            'title' => 'some',
            'id'    => 100001,
        ];

        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/search/index/save');

        $this->assertSessionMessages(
            $this->contains('This search index no longer exists.'),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Stopword\Save::execute
     */
    public function testExecuteNoData()
    {
        $this->dispatch('backend/search/index/save');

        $this->assertSessionMessages(
            $this->contains('No data to save.'),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Stopword\Save::execute
     */
    //    public function testExecuteWrongData()
    //    {
    //        $data = [
    //            'stopword' => 'two words',
    //            'store_id' => 1,
    //        ];
    //
    //        $this->getRequest()->setPostValue($data);
    //
    //        $this->dispatch('backend/search/stopword/save');
    //
    //        $this->assertSessionMessages(
    //            $this->contains('Stopword "two words" can contain only one word.'),
    //            MessageInterface::TYPE_ERROR
    //        );
    //    }
}
