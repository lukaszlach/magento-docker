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
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_core_urlrewrite')
        )->addColumn(
            'urlrewrite_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Url Rewrite Id'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Url Key'
        )->addColumn(
            'module',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Module'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type'
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Entity Id'
        )->addIndex(
            $installer->getIdxName(
                'core_urlrewrite_index1',
                ['module', 'type', 'entity_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['module', 'type', 'entity_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName(
                'core_urlrewrite_index2',
                ['url_key', 'module'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['url_key', 'module'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $installer->getConnection()->createTable($table);
    }
}
