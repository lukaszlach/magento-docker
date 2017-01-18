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


namespace Mirasvit\Search\Controller\Adminhtml\Synonym;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

class SaveTest extends AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Mirasvit_Search::search_synonym';
        $this->uri = 'backend/search/synonym/save';

        parent::setUp();
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Synonym\Save::execute
     */
    public function testExecuteSuccess()
    {
        $data = [
            'synonyms' => 'some, other, else',
            'store_id' => 1,
        ];

        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/search/synonym/save');

        $this->assertSessionMessages(
            $this->contains('You saved the synonym.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Synonym\Save::execute
     */
    public function testExecuteNotExists()
    {
        $data = [
            'synonyms' => 'some, other, else',
            'store_id' => 1,
            'id'       => 100001,
        ];

        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/search/synonym/save');

        $this->assertSessionMessages(
            $this->contains('This synonym no longer exists.'),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Stopword\Save::execute
     */
    public function testExecuteNoData()
    {
        $this->dispatch('backend/search/stopword/save');

        $this->assertSessionMessages(
            $this->contains('No data to save.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
