<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 09/02/2017
 * Time: 11:48
 */
namespace Magenest\ZohocrmIntegration\Setup;

use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $this->createQueueTable($installer);
        }

        $installer->endSetup();
    }

    /**
     * Create the table magenest_zohocrm_queue
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createQueueTable($installer)
    {
        $tableName = 'magenest_zohocrm_queue';
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
                ],
                'Id'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                45,
                ['nullable' => true],
                'Entity Type'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Entity Id'
            )
            ->addColumn(
                'enqueue_time',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Enqueue Time'
            )
            ->addColumn(
                'priority',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Enqueue Time'
            )
            ->setComment('ZohoCrm Sync Queue');

        $installer->getConnection()->createTable($table);
    }
}
