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


namespace Mirasvit\Search\Helper;

use Mirasvit\Search\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var \Mirasvit\Search\Model\Config
     */
    protected $config;

    /**
     * @param Config  $config
     * @param Context $context
     */
    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config = $config;

        parent::__construct($context);
    }


    /**
     * @param string $string
     * @return string
     */
    public function prepareDataIndex($string)
    {
        $string = strip_tags($string);

        $expressions = $this->config->getLongTailExpressions();

        foreach ($expressions as $expr) {
            $matches = null;
            preg_match_all($expr['match_expr'], $string, $matches);

            foreach ($matches[0] as $math) {
                $math = preg_replace($expr['replace_expr'], $expr['replace_char'], $math);
                $string .= ' ' . $math;
            }
        }

        return ' ' . $string . ' ';
    }

    /**
     * @param \Magento\Framework\DB\Ddl\Table $table
     * @return $this
     */
    public function prepareTemporaryTable($table)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Mirasvit\Search\Model\IndexFactory $indexFactory */
        $indexFactory = $objectManager->get('Mirasvit\Search\Model\IndexFactory');

        $index = $indexFactory->create()->load('catalogsearch_fulltext');
        if (!$index->hasProperty('only_active_categories')) {
            return $this;
        }

        /** @var \Magento\Catalog\Model\Category\Tree $tree */
        $tree = $objectManager->get('Magento\Catalog\Model\Category\Tree');

        $root = $tree->getTree($tree->getRootNode())->getChildrenData();

        $ids = $this->getActiveCategories($root);

        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $prods */
        $prods = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection');

        $prods->addCategoriesFilter(['eq' => $ids]);

        $select = $prods->getSelect()
            ->reset('columns')
            ->columns(['entity_id']);

        $connection->delete($table->getName(), ['entity_id NOT IN(' . (new \Zend_Db_Expr($select)) . ')']);

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Category $root
     * @return array
     */
    protected function getActiveCategories($root = null)
    {
        $result = [];
        foreach ($root as $item) {
            if ($item->getIsActive()) {
                $result[] = $item->getId();
                if ($item->getChildrenData()) {
                    foreach ($this->getActiveCategories($item->getChildrenData()) as $id) {
                        $result[] = $id;
                    }
                }
            }
        }

        return $result;
    }
}
