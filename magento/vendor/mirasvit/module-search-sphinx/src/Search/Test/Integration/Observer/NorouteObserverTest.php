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


namespace Mirasvit\Search\Observer;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class NorouteObserverTest extends AbstractController
{
    public function testExecute()
    {
        /** @var \Magento\Framework\App\MutableScopeConfig $scopeConfig */
        $scopeConfig = Bootstrap::getObjectManager()->create('Magento\Framework\App\MutableScopeConfig');

        // Enabled
        $scopeConfig->setValue('search/advanced/noroute_to_search', 1);

        $this->dispatch('women/women_special-dress.html');

        $expect = new \PHPUnit_Framework_Constraint_IsEqual(
            'http://localhost/index.php/catalogsearch/result/?q=women+special+dress'
        );

        $this->assertRedirect($expect);

        // Disabled
        $scopeConfig->setValue('search/advanced/noroute_to_search', 0);

        $this->dispatch('women/women_special-dress.html');

        $this->assert404NotFound();
    }
}
