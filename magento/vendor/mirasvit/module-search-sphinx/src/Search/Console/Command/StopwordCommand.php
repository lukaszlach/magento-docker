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


namespace Mirasvit\Search\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Command\Command;
use Mirasvit\Search\Model\StopwordFactory;
use Mirasvit\Search\Model\Config;

class StopwordCommand extends Command
{
    /**
     * @var StopwordFactory
     */
    protected $stopwordFactory;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param StopwordFactory $stopwordFactory
     * @param StoreManager    $storeManager
     * @param Config          $config
     */
    public function __construct(
        StopwordFactory $stopwordFactory,
        StoreManager $storeManager,
        Config $config
    ) {
        $this->stopwordFactory = $stopwordFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Stopwords file'
            ),
            new InputOption(
                'store',
                null,
                InputOption::VALUE_REQUIRED,
                'Store Id'
            ),
            new InputOption(
                'remove',
                null,
                InputOption::VALUE_NONE,
                'remove'
            )
        ];

        $this->setName('mirasvit:search:stopword')
            ->setDescription('Import stopwords')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('remove')) {
            $store = $input->getOption('store');

            $collection = $this->stopwordFactory->create()->getCollection();
            if ($store) {
                $collection->addFieldToFilter('store_id', $store);
            }

            $cnt = 0;
            foreach ($collection as $item) {
                $item->delete();
                $cnt++;

                if ($cnt % 1000 == 0) {
                    $output->writeln("<info>$cnt stopwords are removed...</info>");
                }
            }

            $output->writeln("<info>$cnt stopwords are removed.</info>");

            return;
        }

        if ($input->getOption('file') && $input->getOption('store')) {
            $file = $this->config->getStopwordDirectoryPath() . DIRECTORY_SEPARATOR . $input->getOption('file');
            $store = $input->getOption('store');

            $result = $this->stopwordFactory->create()
                ->import($file, $store);

            $output->writeln("<info>Imported {$result['stopwords']} stopwords</info>");
        } else {
            $output->writeln('<info>Available files:</info>');
            foreach ($this->stopwordFactory->create()->getAvailableFiles() as $file) {
                $info = pathinfo($file);
                $output->writeln("    {$info['basename']}");
            }

            $output->writeln('<info>Available stores:</info>');
            foreach ($this->storeManager->getStores(true) as $store) {
                $output->writeln("    {$store->getId()} [{$store->getCode()}]");
            }
        }

    }
}
