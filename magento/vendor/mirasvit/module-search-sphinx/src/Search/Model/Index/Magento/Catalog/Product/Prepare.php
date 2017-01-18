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


//@codingStandardsIgnoreFile
namespace Mirasvit\Search\Model\Index\Magento\Catalog\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\Full;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Search\Helper\Data as SearchHelper;
use Mirasvit\Search\Model\IndexFactory;

class Prepare
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * Constructor
     *
     * @param SearchHelper           $searchHelper
     * @param IndexFactory           $indexFactory
     * @param ObjectManagerInterface $objectManager
     * @param ResourceConnection     $resource
     */
    public function __construct(
        SearchHelper $searchHelper,
        IndexFactory $indexFactory,
        ObjectManagerInterface $objectManager,
        ResourceConnection $resource
    ) {
        $this->indexFactory = $indexFactory;
        $this->searchHelper = $searchHelper;
        $this->resource = $resource;
        $this->objectManager = $objectManager;
        $this->connection = $this->resource->getConnection();
    }

    public function prepareBatchDocuments(&$documents)
    {
        $this->addCategoryData($documents);
        $this->addCustomOptions($documents);
        $this->addBundledOptions($documents);
        $this->addProductIdData($documents);

        foreach ($documents as $key => $value) {
            foreach ($value as $attrId => $data) {
                $value[$attrId] = $this->searchHelper->prepareDataIndex($data);
            }

            $documents[$key] = $value;
        }

        return $documents;
    }

    /**
     * @param array $index
     * @return $this
     */
    protected function addCustomOptions(&$index)
    {
        if (!$this->getIndex()->hasProperty('include_custom_options')) {
            return $this;
        }

        $productIds = array_keys($index);
        $this->connection->query('SET SESSION group_concat_max_len = 1000000;');

        $select = $this->connection->select()
            ->from(['main_table' => $this->resource->getTableName('catalog_product_option')], ['product_id'])
            ->joinLeft(
                ['otv' => $this->resource->getTableName('catalog_product_option_type_value')],
                'main_table.option_id = otv.option_id',
                ['sku' => new \Zend_Db_Expr("GROUP_CONCAT(otv.`sku` SEPARATOR ' ')")]
            )
            ->joinLeft(
                ['ott' => $this->resource->getTableName('catalog_product_option_type_title')],
                'otv.option_type_id = ott.option_type_id',
                ['title' => new \Zend_Db_Expr("GROUP_CONCAT(ott.`title` SEPARATOR ' ')")]
            )
            ->where('main_table.product_id IN (?)', $productIds)
            ->group('product_id');

        foreach ($this->connection->fetchAll($select) as $row) {
            if (!isset($index[$row['product_id']]['options'])) {
                $index[$row['product_id']]['options'] = '';
            }
            $index[$row['product_id']]['options'] .= ' ' . $row['title'];
            $index[$row['product_id']]['options'] .= ' ' . $row['sku'];
        }

        return $this;
    }

    /**
     * @param array $index
     * @return $this
     */
    protected function addBundledOptions(&$index)
    {
        if (!$this->getIndex()->hasProperty('include_bundled')) {
            return $this;
        }

        $productIds = array_keys($index);
        $this->connection->query('SET SESSION group_concat_max_len = 1000000;');

        $select = $this->connection->select()
            ->from(
                ['main_table' => $this->resource->getTableName('catalog_product_entity')],
                ['sku' => new \Zend_Db_Expr("GROUP_CONCAT(main_table.`sku` SEPARATOR ' ')")]
            )
            ->joinLeft(
                ['cpr' => $this->resource->getTableName('catalog_product_relation')],
                'main_table.entity_id = cpr.child_id',
                ['parent_id']
            )
            ->where('cpr.parent_id IN (?)', $productIds)
            ->group('cpr.parent_id');


        foreach ($this->connection->fetchAll($select) as $row) {
            if (!isset($index[$row['parent_id']]['options'])) {
                $index[$row['parent_id']]['options'] = '';
            }
            $index[$row['parent_id']]['options'] .= ' ' . $row['sku'];
        }

        return $this;
    }

    /**
     * @param array $index
     * @return $this
     */
    protected function addProductIdData(&$index)
    {
        if (!$this->getIndex()->hasProperty('include_id')) {
            return $this;
        }

        foreach ($index as $entityId => &$data) {
            if (!isset($data['options'])) {
                $data['options'] = '';
            }

            $data['options'] .= ' ' . $entityId;
        }

        return $this;
    }

    /**
     * @param array $index
     * @return $this
     */
    protected function addCategoryData(&$index)
    {
        if (!$this->getIndex()->hasProperty('include_category')) {
            return $this;
        }

        $entityTypeId = $this->objectManager->create('Magento\Eav\Model\Entity')
            ->setType(Category::ENTITY)->getTypeId();

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->loadByCode($entityTypeId, 'name');

        $productIds = array_keys($index);

        $valueSelect = $this->connection->select()
            ->from(
                ['cc' => $this->resource->getTableName('catalog_category_entity')],
                [new \Zend_Db_Expr("GROUP_CONCAT(vc.value SEPARATOR ' ')")]
            )
            ->joinLeft(
                ['vc' => $attribute->getBackend()->getTable()],
                'cc.entity_id = vc.entity_id',
                []
            )
            ->where("LOCATE(CONCAT('/', CONCAT(cc.entity_id, '/')), CONCAT(ce.path, '/'))")
            ->where('vc.attribute_id = ?', $attribute->getId());

        $columns = [
            'product_id' => 'product_id',
            'category'   => new \Zend_Db_Expr('(' . $valueSelect . ')'),
        ];

        $select = $this->connection->select()
            ->from([$this->resource->getTableName('catalog_category_product')], $columns)
            ->joinLeft(
                ['ce' => $this->resource->getTableName('catalog_category_entity')],
                'category_id = ce.entity_id',
                []
            )
            ->where('product_id IN (?)', $productIds);

        foreach ($this->connection->fetchAll($select) as $row) {
            if (!isset($index[$row['product_id']]['options'])) {
                $index[$row['product_id']]['options'] = '';
            }
            $index[$row['product_id']]['options'] .= ' ' . $row['category'];
        }

        return $this;
    }

    /**
     * @return \Mirasvit\Search\Model\Index
     */
    protected function getIndex()
    {
        return $this->indexFactory->create()->load('catalogsearch_fulltext');
    }
}
