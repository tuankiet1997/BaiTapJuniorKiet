<?php

namespace Magenest\Attachment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if(version_compare($context->getVersion(), '1.0.1', '<')) { $setup->getConnection()->addIndex(
            $setup->getTable('magenest_attachment'),
            'unique_index_name',
            array('file_name', 'file_label'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        );
        }

        $setup->endSetup();
    }
}
