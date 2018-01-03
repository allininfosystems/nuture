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
 * @package   mirasvit/module-kb
 * @version   1.0.26
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Kb\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer->getConnection()
                ->addColumn($installer->getTable('mst_kb_category'), 'display_mode', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => 255,
                    'default'  => '',
                    'comment'  => 'Display mode',
                ]);
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $installer->getConnection()
                ->modifyColumn($installer->getTable('mst_kb_article'), 'created_at', [
                    'type'     => Table::TYPE_TIMESTAMP,
                    'unsigned' => false,
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ]);
        }
    }
}
