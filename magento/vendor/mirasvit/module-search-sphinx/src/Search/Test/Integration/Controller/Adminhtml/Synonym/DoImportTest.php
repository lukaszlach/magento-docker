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

use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\Framework\Message\MessageInterface;

class DoImportTest extends AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Mirasvit_Search::search_synonym';
        $this->uri = 'backend/search/synonym/doImport';

        parent::setUp();
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Synonym\DoImport::execute
     */
    public function testExecuteNoData()
    {
        $this->dispatch('backend/search/synonym/doImport');
        $this->assertSessionMessages(
            $this->contains('No data to import.'),
            MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @covers Mirasvit\Search\Controller\Adminhtml\Synonym\DoImport::execute
     */
    public function testExecuteSuccess()
    {
        $data = [
            'file'     => dirname(dirname(dirname(dirname(__FILE__)))) . '/_files/Controller/Synonym/synonyms.txt',
            'store_id' => 1,
        ];

        $this->getRequest()->setPostValue($data);

        $this->dispatch('backend/search/synonym/doImport');

        $this->assertSessionMessages(
            $this->contains('Imported 7 synonym(s).'),
            MessageInterface::TYPE_SUCCESS
        );
    }
}
