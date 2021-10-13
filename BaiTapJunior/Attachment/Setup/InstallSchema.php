<?php

namespace Magenest\Attachment\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('magenest_attachment')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_attachment')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'file_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'File Name'
                )
                ->addColumn(
                    'mine_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Mine Type'
                )
                ->addColumn(
                    'file_extension',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Extension File'
                )
                ->addColumn(
                    'file_label',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Label File'
                )
                ->addColumn(
                    'customer_group_ids',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Group Customer'
                )
                ->addColumn(
                    'file_detail',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [],
                    'File Detail'
                )
                ->addColumn(
                    'file_size',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Size File'
                )
                ->addColumn(
                    'include_order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Order Include'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    [],
                    'Status'
                )

                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, 'on_update' => false],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, 'on_update' => true],
                    'Updated At'
                )
                ->setComment('Table');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
