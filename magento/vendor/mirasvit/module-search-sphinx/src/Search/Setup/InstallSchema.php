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


namespace Mirasvit\Search\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Create table 'mst_search_index'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_search_index')
        )->addColumn(
            'index_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Index Id'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Index Code'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )->addColumn(
            'position',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => '0'],
            'Position'
        )->addColumn(
            'attributes_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Attributes'
        )->addColumn(
            'properties_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Properties'
        )->addColumn(
            'status',
            Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => '0'],
            'Status'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Update date'
        )->setComment(
            'Search Index'
        );

        $installer->getConnection()->createTable($table);
    }
}
