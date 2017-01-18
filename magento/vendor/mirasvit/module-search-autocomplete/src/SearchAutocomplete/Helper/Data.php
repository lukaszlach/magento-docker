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


namespace Mirasvit\SearchAutocomplete\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\SearchAutocomplete\Model\Config;

class Data extends AbstractHelper
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     * gi
     * @param ObjectManagerInterface $objectManager
     * @param Config                 $config
     * @param Context                $context
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config,
        Context $context
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $context->getModuleManager();
        $this->config = $config;

        return parent::__construct($context);
    }

    /**
     * List of available search indexes
     *
     * @return DataObject[]
     */
    public function getAvailableIndexes()
    {
        $indexes = [];

        if ($this->moduleManager->isEnabled('Mirasvit_Search')) {
            $indexCollectionFactory = $this->objectManager
                ->create('\Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory');

            $collection = $indexCollectionFactory->create()
                ->addFieldToFilter('is_active', true);

            foreach ($collection as $index) {
                $indexes[] = $index;
            }
        } else {
            $indexes[] = new DataObject([
                'title' => __('Products')->__toString(),
                'code'  => 'catalogsearch_fulltext',
            ]);
        }

        $indexes[] = new DataObject([
            'title' => __('Popular suggestions')->__toString(),
            'code'  => 'magento_search_query',
        ]);

        return $indexes;
    }

    /**
     * List of enabled indexes for autocomplete
     *
     * @return array
     */
    public function getEnabledIndexes()
    {
        $configuration = $this->config->getIndexConfiguration();
        $indexes = [];

        foreach ($this->getAvailableIndexes() as $index) {
            if (isset($configuration[$index->getData('code')])) {
                $data = $configuration[$index->getData('code')];
                if (isset($data['is_active']) && $data['is_active']) {
                    $index->setData('order', $data['order']);
                    $index->setData('limit', $data['limit']);
                    $indexes[] = $index;
                }
            }
        }

        usort($indexes, function ($a, $b) {
            return $a->getData('order') - $b->getData('order');
        });

        return $indexes;
    }
}
