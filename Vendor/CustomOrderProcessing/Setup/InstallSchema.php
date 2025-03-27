<?php
namespace Vendor\CustomOrderProcessing\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Create custom table
        $table = $setup->getConnection()->newTable($setup->getTable('order_status_log'))
            ->addColumn(
                'log_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Log ID'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order ID'
            )
            ->addColumn(
                'old_status',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Old Status'
            )
            ->addColumn(
                'new_status',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'New Status'
            )
            ->addColumn(
                'changed_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Timestamp'
            )
            ->setComment('Order Status Change Log');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}