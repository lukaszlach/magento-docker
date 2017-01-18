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
 * @package   mirasvit/module-misspell
 * @version   1.0.7
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Misspell\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Mirasvit\Misspell\Helper\Text as TextHelper;

class Indexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'mst_misspell';

    /**
     * @var array
     */
    protected $allowedTables = [
        'catalogsearch_fulltext',
        'mst_searchindex_',
        'catalog_product_entity_text',
        'catalog_product_entity_varchar',
        'catalog_category_entity_text',
        'catalog_category_entity_varchar',
    ];

    /**
     * @var array
     */
    protected $disallowedTables = [
        'mst_searchindex_mage_catalogsearch_query',
    ];

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Mirasvit\Misspell\Helper\Text
     */
    protected $text;

    /**
     * Constructor
     *
     * @param ResourceConnection $resource
     * @param TextHelper         $textHelper
     */
    public function __construct(
        ResourceConnection $resource,
        TextHelper $textHelper
    ) {
        $this->resource = $resource;
        $this->connection = $this->resource->getConnection();
        $this->text = $textHelper;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $results = [];
        foreach ($this->getTables() as $table => $columns) {
            if (!count($columns)) {
                continue;
            }

            foreach ($columns as $idx => $col) {
                $columns[$idx] = '`' . $col . '`';
            }

            $select = $this->connection->select();
            $fromColumns = new \Zend_Db_Expr('CONCAT(' . implode(",' ',", $columns) . ') as data_index');
            $select->from($table, $fromColumns);

            $result = $this->connection->query($select);
            while ($row = $result->fetch()) {
                $data = $row['data_index'];

                $this->split($data, $results);
            }
        }

        $indexTable = $this->resource->getTableName('mst_misspell_index');
        $this->connection->delete($indexTable);

        $rows = [];
        foreach ($results as $word => $freq) {
            $rows[] = [
                'keyword'   => $word,
                'trigram'   => $this->text->getTrigram($word),
                'frequency' => $freq / count($results),
            ];

            if (count($rows) > 1000) {
                $this->connection->insertArray($indexTable, ['keyword', 'trigram', 'frequency'], $rows);
                $rows = [];
            }
        }

        if (count($rows) > 0) {
            $this->connection->insertArray($indexTable, ['keyword', 'trigram', 'frequency'], $rows);
        }

        $this->connection->delete($this->resource->getTableName('mst_misspell_suggest'));
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function executeList(array $ids)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function executeRow($id)
    {
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($ids)
    {
    }

    /**
     * Split string to words
     *
     * @param string $string
     * @param array  &$results
     * @param int    $increment
     * @return void
     */
    protected function split($string, &$results, $increment = 1)
    {
        $string = $this->text->cleanString($string);
        $words = $this->text->splitWords($string);

        foreach ($words as $word) {
            if ($this->text->strlen($word) >= $this->text->getGram()
                && !is_numeric($word)
                && $this->text->strlen($word) <= 10
            ) {
                $word = $this->text->strtolower($word);
                if (!isset($results[$word])) {
                    $results[$word] = $increment;
                } else {
                    $results[$word] += $increment;
                }
            }
        }
    }


    /**
     * List of tables that follow allowedTables, disallowedTables conditions
     *
     * @return array
     */
    protected function getTables()
    {
        $result = [];
        $tables = $this->connection->getTables();

        foreach ($tables as $table) {
            $isAllowed = false;

            foreach ($this->allowedTables as $allowedTable) {
                if (strpos($table, $allowedTable) !== false) {
                    $isAllowed = true;
                }
            }

            foreach ($this->disallowedTables as $disallowedTable) {
                if (strpos($table, $disallowedTable) !== false) {
                    $isAllowed = false;
                }
            }

            if (!$isAllowed) {
                continue;
            }

            $result[$table] = $this->getTextColumns($table);
        }

        return $result;
    }

    /**
     * Text columns
     *
     * @param string $table Database table name
     * @return array list of columns with text type
     */
    protected function getTextColumns($table)
    {
        $result = [];
        $allowedTypes = ['text', 'varchar', 'mediumtext', 'longtext'];
        $columns = $this->connection->describeTable($table);

        foreach ($columns as $column => $info) {
            if (in_array($info['DATA_TYPE'], $allowedTypes)) {
                $result[] = $column;
            }
        }

        return $result;
    }
}
