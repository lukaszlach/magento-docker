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
use Symfony\Component\Console\Command\Command;
use Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory as IndexCollectionFactory;
use Magento\Framework\App\State as AppState;

class ReindexCommand extends Command
{
    /**
     * @var IndexCollectionFactory
     */
    protected $indexCollectionFactory;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @param IndexCollectionFactory $indexCollectionFactory
     * @param AppState               $appState
     */
    public function __construct(
        IndexCollectionFactory $indexCollectionFactory,
        AppState $appState
    ) {
        $this->indexCollectionFactory = $indexCollectionFactory;
        $this->appState = $appState;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:search:reindex')
            ->setDescription('Reindex all search indexes')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//
//        /** @var \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory */
//        $eavSetupFactory = $objectManager->get('Magento\Eav\Setup\EavSetupFactory');
//
//        $setup = $objectManager->get('Magento\Framework\Setup\ModuleDataSetupInterface');
//
//        $eavSetup = $eavSetupFactory->create(['setup' => $setup]);
//
//        for ($i = 1; $i < 400; $i++) {
//            $eavSetup->addAttribute(
//                \Magento\Catalog\Model\Product::ENTITY,
//                substr(md5($i), 0, 5) . '_sample_attribute_' . $i,
//                [
//                    'type'                    => 'int',
//                    'backend'                 => '',
//                    'frontend'                => '',
//                    'label'                   => 'Sample Atrribute ' . $i,
//                    'input'                   => '',
//                    'class'                   => '',
//                    'source'                  => '',
//                    'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
//                    'visible'                 => true,
//                    'required'                => false,
//                    'user_defined'            => true,
//                    'default'                 => '',
//                    'searchable'              => true,
//                    'filterable'              => true,
//                    'comparable'              => true,
//                    'visible_on_front'        => true,
//                    'used_in_product_listing' => true,
//                    'unique'                  => false,
//                    'apply_to'                => ''
//                ]
//            );
//
//            echo $i . PHP_EOL;
//        }
//
//        die();

        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        $collection = $this->indexCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);

        /** @var \Mirasvit\Search\Model\Index $index */
        foreach ($collection as $index) {
            $output->write($index->getTitle() . ' [' . $index->getCode() . ']....');

            try {
                $index->getIndexInstance()->reindexAll();
                $output->writeln("<info>Done</info>");
            } catch (\Exception $e) {
                $output->writeln("Error");
                $output->writeln($e->getMessage());
            }
        }
    }
}
