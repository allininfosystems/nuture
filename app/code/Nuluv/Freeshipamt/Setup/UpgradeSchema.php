<?php

namespace Nuluv\Freeshipamt\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';

		
        //Quote address tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteAddressTable),
                'freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Freeshipamt'
                ]
            );
        $setup->getConnection()
            ->addColumn(
              $setup->getTable($quoteAddressTable),
                'base_freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Freeshipamt'
                ]
            );
        //Quote tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Freeshipamt'

                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'base_freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Freeshipamt'

                ]
            );
        //Order tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Freeshipamt'

                ]
            );

         $setup->getConnection()
             ->addColumn(
                $setup->getTable($orderTable),
                'base_freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Freeshipamt'

                ]
            );
        //Invoice tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Freeshipamt'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'base_freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Freeshipamt'

                ]
            );
        //Credit memo tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Freeshipamt'

                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'base_freeshipamt',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'=>'12,4',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Base Freeshipamt'

                ]
            );


        $setup->endSetup();
    }
}
