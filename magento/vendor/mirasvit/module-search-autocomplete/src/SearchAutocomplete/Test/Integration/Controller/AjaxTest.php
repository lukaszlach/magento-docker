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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.0.36
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Controller;

use Magento\TestFramework\TestCase\AbstractController;

class ResultTest extends AbstractController
{
    /**
     * @magentoDataFixture Mirasvit/SearchAutocomplete/_files/products.php
     */
    public function testSuggestAction()
    {
        $this->getRequest()->setParams(
            [
                'q' => 'test',
            ]
        );
        $this->dispatch('searchautocomplete/ajax/suggest');

        $body = $this->getResponse()->getBody();

        $this->assertContains('totalItems', $body);
        $this->assertContains('success', $body);
    }
}
