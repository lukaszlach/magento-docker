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

class MakeConfigCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\SearchSphinx\Console\Command\MakeConfigCommand
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        $this->command = Bootstrap::getObjectManager()
            ->create('\Mirasvit\SearchSphinx\Console\Command\MakeConfigCommand');
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @covers \Mirasvit\SearchSphinx\Console\Command\MakeConfigCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute([]);

        $this->assertContains('Sphinx configuration file successfully generated.', $this->commandTester->getDisplay());
        $this->assertContains('/var/sphinx/sphinx.conf', $this->commandTester->getDisplay());
    }
}
