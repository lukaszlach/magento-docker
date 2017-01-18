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


namespace Mirasvit\Misspell\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Create table 'mst_misspell_index'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_misspell_index')
        )->addColumn(
            'index_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Index Id'
        )->addColumn(
            'keyword',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Keyword'
        )->addColumn(
            'trigram',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Trigram'
        )->addColumn(
            'frequency',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Frequency'
        )->addIndex(
            $installer->getIdxName(
                'mst_misspell_index',
                'trigram',
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            'trigram',
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Misspell Index'
        );

        $installer->getConnection()->createTable($table);


        /**
         * Create table 'mst_misspell_suggest'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_misspell_suggest')
        )->addColumn(
            'suggest_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Suggest Id'
        )->addColumn(
            'query',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Query'
        )->addColumn(
            'suggest',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Suggest'
        )->setComment(
            'Misspell Suggest'
        );

        $installer->getConnection()->createTable($table);
    }
}
