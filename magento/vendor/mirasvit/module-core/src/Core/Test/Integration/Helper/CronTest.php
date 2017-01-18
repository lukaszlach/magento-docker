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
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Helper;

use Magento\TestFramework\Helper\Bootstrap;

class CronTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Core\Helper\Cron
     */
    protected $helper;

    /**
     * @covers \Mirasvit\Core\Helper\Cron::CheckCronStatus
     */
    public function testCheckCronStatus()
    {
        $this->helper = Bootstrap::getObjectManager()->create('Mirasvit\Core\Helper\Cron');

        list($status, $message) = $this->helper->checkCronStatus(false, false);

        $this->assertFalse($status);
        $this->assertNotEmpty($message);
    }
}
