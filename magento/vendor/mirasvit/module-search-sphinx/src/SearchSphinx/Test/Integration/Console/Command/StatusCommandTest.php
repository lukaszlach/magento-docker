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


namespace Mirasvit\SearchSphinx\Console\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoDbIsolation disabled
 */
class StatusCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\SearchSphinx\Console\Command\StatusCommand
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @var \Mirasvit\SearchSphinx\Model\Engine
     */
    protected $engine;

    public function setUp()
    {
        $this->command = Bootstrap::getObjectManager()->create('\Mirasvit\SearchSphinx\Console\Command\StatusCommand');
        $this->engine = Bootstrap::getObjectManager()->create('\Mirasvit\SearchSphinx\Model\Engine');
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @covers \Mirasvit\SearchSphinx\Console\Command\StatusCommand::execute
     * @magentoDataFixture Mirasvit/SearchSphinx/_files/Console/indexes.php
     */
    public function testExecute()
    {
        $this->engine->stop();

        $this->commandTester->execute([]);
        $this->assertContains('Sphinx daemon not running', $this->commandTester->getDisplay());

        $this->engine->makeConfig();
        $this->engine->start();

        $this->commandTester->execute([]);
        $this->assertContains('searchd status', $this->commandTester->getDisplay());

        $this->engine->stop();
    }
}
