<?php
/**
 * Copyright © 2015 Magecomp. All rights reserved.
 */

namespace Magecomp\Gstcharge\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

			$installer->startSetup();
			
			$eavTable = $installer->getTable('quote');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_charge',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_charge',
					'length'    => '10,2',
				],
				'excl_price' => [
					'type' => Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'excl_price',
										
				],
				'buyer_gst_number' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'Buyer Gst Number',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
		
			$eavTable = $installer->getTable('quote_address');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					 'length'    => '10,2',
				],
				'base_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					 'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',					
				],
				
				'shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_igst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_igst_charge',
					'length'    => '10,2',					
				],
				
		
			];
		
			
			
			$eavTable = $installer->getTable('quote_item');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'cgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_percent',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_charge',
					'length'    => '10,2',
				],
				'sgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_percent',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_charge',
					'length'    => '10,2',
				],
				'igst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_percent',
					'length'    => '10,2',					
				],
				'excl_price' => [
					'type' => Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'excl_price',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
			$eavTable = $installer->getTable('sales_order_grid');
		
			$columns = [
				'buyer_gst_number' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'Buyer Gst Number',
				],
			];
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
			$eavTable = $installer->getTable('sales_order');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'base_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_igst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_igst_charge',
					'length'    => '10,2',					
				],
				'buyer_gst_number' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'Buyer Gst Number',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			$eavTable = $installer->getTable('sales_invoice');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'base_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_igst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_igst_charge',
					'length'    => '10,2',					
				],
				'buyer_gst_number' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'Buyer Gst Number',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			$eavTable = $installer->getTable('sales_creditmemo');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'base_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
				],
				'base_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'base_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'base_cgst_charge',
					'length'    => '10,2',
				],
				'shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_sgst_charge',
					'length'    => '10,2',					
				],
				'shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_cgst_charge',
					'length'    => '10,2',					
				],
				'shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'shipping_igst_charge',
					'length'    => '10,2',					
				],
				'percent_shipping_igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'percent_shipping_igst_charge',
					'length'    => '10,2',					
				],
				'buyer_gst_number' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'Buyer Gst Number',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
		
		$eavTable = $installer->getTable('sales_order_item');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'cgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_percent',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_charge',
					'length'    => '10,2',
				],
				'sgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_percent',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_charge',
					'length'    => '10,2',
				],
				'igst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_percent',
					'length'    => '10,2',
				],
				'excl_price' => [
					'type' => Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'excl_price',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
			
			$eavTable = $installer->getTable('sales_creditmemo_item');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'cgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_percent',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_charge',
					'length'    => '10,2',
				],
				'sgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_percent',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_charge',
					'length'    => '10,2',
				],
				'igst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_percent',
					'length'    => '10,2',
				],
				'excl_price' => [
					'type' => Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'excl_price',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
			$eavTable = $installer->getTable('sales_invoice_item');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'cgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_percent',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_charge',
					'length'    => '10,2',
				],
				'sgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_percent',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_charge',
					'length'    => '10,2',
				],
				'igst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_percent',
					'length'    => '10,2',
				],
				'excl_price' => [
					'type' => Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'excl_price',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
			$eavTable = $installer->getTable('sales_shipment_item');
		
			$columns = [
				'cgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_charge',
					'length'    => '10,2',
				],
				'cgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'cgst_percent',
					'length'    => '10,2',
				],
				'sgst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_charge',
					'length'    => '10,2',
				],
				'sgst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'sgst_percent',
					'length'    => '10,2',
				],
				'igst_charge' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_charge',
					'length'    => '10,2',
				],
				'igst_percent' => [
					'type' => Table::TYPE_DECIMAL,
					'nullable' => false,
					'comment' => 'igst_percent',
					'length'    => '10,2',
				],
				'excl_price' => [
					'type' => Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'excl_price',
										
				],
		
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}
			
			
		
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();


$sql = "SELECT * FROM directory_country_region where country_id = 'IN'" ;
$result = $connection->fetchAll($sql); 


if(count($result) == 0){		
		
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES  ('IN', 'AP', 'Andhra Pradesh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Andhra Pradesh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'AR', 'Arunachal Pradesh')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Arunachal Pradesh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'AS', 'Assam')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Assam')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'BR', 'Bihar')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Bihar')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'CG', 'Chhattisgarh')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Chhattisgarh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'GA', 'Goa')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Goa')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'GJ', 'Gujarat')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Gujarat')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'HR', 'Haryana')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Haryana')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'HP', 'Himachal Pradesh')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Himachal Pradesh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'JK', 'Jammu and Kashmir')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Jammu and Kashmir')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'JH', 'Jharkhand')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Jharkhand')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'KA', 'Karnataka')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Karnataka')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'KL', 'Kerala')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Kerala')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'MP', 'Madhya Pradesh')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Madhya Pradesh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'MH', 'Maharashtra')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Maharashtra')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'MN', 'Manipur')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Manipur')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'ML', 'Meghalaya')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Meghalaya')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'MZ', 'Mizoram')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Mizoram')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'NL', 'Nagaland')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Nagaland')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'OR', 'Orissa')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Orissa')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'PB', 'Punjab')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Punjab')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'RJ', 'Rajasthan')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Rajasthan')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'SK', 'Sikkim')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Sikkim')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'TR', 'Tripura')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Tripura')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'UK', 'Uttarakhand')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Uttarakhand')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'UP', 'Uttar Pradesh')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Uttar Pradesh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'WB', 'West Bengal')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'West Bengal')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'TN', 'Tamil Nadu')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Tamil Nadu')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'AN', 'Andaman and Nicobar Islands')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Andaman and Nicobar Islands')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'CH', 'Chandigarh')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Chandigarh')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'DH', 'Dadra and Nagar Haveli')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Dadra and Nagar Haveli')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'DD', 'Daman and Diu')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Daman and Diu')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'DL', 'Delhi')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Delhi')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'LD', 'Lakshadweep')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Lakshadweep')");

$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'PY', 'Pondicherry')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Pondicherry')");
	
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
    ('IN', 'TS', 'Telangana')");
$setup->getConnection()->query("INSERT INTO `{$installer->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
    ('en_US', LAST_INSERT_ID(), 'Telangana')");	
	
	
	
		}
			
			$installer->endSetup();
    }
}
